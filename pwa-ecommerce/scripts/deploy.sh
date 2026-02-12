#!/bin/bash
#
# Deploy script for Vietnix Shared Hosting
# Usage: ./scripts/deploy.sh [--skip-migration] [--backup-only] [--help]
#
# This script is meant to be run on the remote server via SSH
# It handles the deployment process for Laravel applications
#

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration (can be overridden by environment variables)
DEPLOY_PATH="${DEPLOY_PATH:-$(pwd)}"
BACKUP_DIR="${BACKUP_DIR:-backups}"
KEEP_BACKUPS="${KEEP_BACKUPS:-5}"
SKIP_MIGRATION="${SKIP_MIGRATION:-false}"
BACKUP_ONLY="${BACKUP_ONLY:-false}"
TELEGRAM_NOTIFY="${TELEGRAM_NOTIFY:-true}"
TELEGRAM_BOT_TOKEN="${TELEGRAM_BOT_TOKEN:-}"
TELEGRAM_CHAT_ID="${TELEGRAM_CHAT_ID:-}"

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1" >&2
}

show_help() {
    cat << EOF
Deploy Script for Laravel on Vietnix Shared Hosting

Usage: $0 [OPTIONS]

Options:
    --skip-migration    Skip database migrations
    --backup-only       Only create backup, don't deploy
    --help              Show this help message

Environment Variables:
    DEPLOY_PATH         Deployment directory path
    BACKUP_DIR          Backup directory name (default: backups)
    KEEP_BACKUPS        Number of backups to keep (default: 5)
    SKIP_MIGRATION      Skip migrations (true/false)
    BACKUP_ONLY         Only backup mode (true/false)
    TELEGRAM_NOTIFY     Enable Telegram notifications (true/false)
    TELEGRAM_BOT_TOKEN  Telegram bot token
    TELEGRAM_CHAT_ID    Telegram chat ID

Examples:
    $0                              # Normal deployment
    $0 --skip-migration             # Deploy without running migrations
    $0 --backup-only                # Only create backup
    DEPLOY_PATH=/var/www $0         # Custom deployment path

EOF
}

# Parse arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --skip-migration)
            SKIP_MIGRATION=true
            shift
            ;;
        --backup-only)
            BACKUP_ONLY=true
            shift
            ;;
        --help|-h)
            show_help
            exit 0
            ;;
        *)
            log_error "Unknown option: $1"
            show_help
            exit 1
            ;;
    esac
done

# Send Telegram notification
send_telegram_notification() {
    local status="$1"
    local message="$2"
    
    if [ "$TELEGRAM_NOTIFY" != "true" ] || [ -z "$TELEGRAM_BOT_TOKEN" ] || [ -z "$TELEGRAM_CHAT_ID" ]; then
        return
    fi
    
    local emoji=""
    if [ "$status" == "success" ]; then
        emoji="🚀"
    else
        emoji="❌"
    fi
    
    local timestamp=$(date -u '+%Y-%m-%d %H:%M:%S UTC')
    
    # Escape markdown characters
    message=$(echo "$message" | sed 's/_/\\-/g' | sed 's/\*/\\*/g' | sed 's/`/\\`/g')
    
    local payload=$(cat << EOF
{
    "chat_id": "$TELEGRAM_CHAT_ID",
    "text": "$emoji **Deploy Update**\n\n$message\n\nTimestamp: $timestamp",
    "parse_mode": "Markdown"
}
EOF
)
    
    curl -s -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage" \
        -H "Content-Type: application/json" \
        -d "$payload" > /dev/null 2>&1 || true
}

# Backup function
create_backup() {
    log_info "Creating backup..."
    
    # Create backup directory if it doesn't exist
    mkdir -p "$BACKUP_DIR"
    
    # Backup .env file
    if [ -f ".env" ]; then
        local backup_file="${BACKUP_DIR}/env_$(date +%Y%m%d_%H%M%S).bak"
        cp .env "$backup_file"
        log_success "Backed up .env to $backup_file"
        
        # Update symlink to latest backup
        ln -sf "$backup_file" "${BACKUP_DIR}/latest.env.bak"
    else
        log_warning ".env file not found, skipping backup"
    fi
    
    # Backup database (if using MySQL and credentials available)
    if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ]; then
        local db_backup_file="${BACKUP_DIR}/database_$(date +%Y%m%d_%H%M%S).sql.gz"
        
        if [ -n "$DB_PASSWORD" ]; then
            MY_PWD="$DB_PASSWORD" mysqldump -h "$DB_HOST" -u "$DB_USERNAME" "$DB_DATABASE" 2>/dev/null | gzip > "$db_backup_file"
        else
            mysqldump -h "$DB_HOST" -u "$DB_USERNAME" "$DB_DATABASE" 2>/dev/null | gzip > "$db_backup_file"
        fi
        
        if [ -f "$db_backup_file" ]; then
            log_success "Database backup created: $db_backup_file"
        fi
    fi
    
    # Clean old backups
    local backup_count=$(ls -1 "${BACKUP_DIR}"/env_*.bak 2>/dev/null | wc -l)
    if [ "$backup_count" -gt "$KEEP_BACKUPS" ]; then
        local to_delete=$((backup_count - KEEP_BACKUPS))
        ls -t "${BACKUP_DIR}"/env_*.bak 2>/dev/null | tail -n +$((to_delete + 1)) | xargs -r rm
        log_info "Cleaned up $to_delete old backups"
    fi
}

# Cleanup old log files
cleanup_logs() {
    log_info "Cleaning up old log files..."
    
    # Remove logs older than 7 days
    find storage/logs -name "*.log" -type f -mtime +7 -delete 2>/dev/null || true
    
    # Clear Laravel cache files
    find bootstrap/cache -name "*.php" -type f -mtime +1 -delete 2>/dev/null || true
    
    log_success "Log cleanup completed"
}

# Main deployment function
deploy() {
    log_info "Starting deployment at $(date)"
    log_info "Deployment path: $DEPLOY_PATH"
    
    # Change to deployment directory
    cd "$DEPLOY_PATH"
    
    # Ensure we're in the right directory
    if [ ! -f "artisan" ]; then
        log_error "Laravel artisan file not found in $DEPLOY_PATH"
        send_telegram_notification "failure" "Deployment failed: artisan file not found"
        exit 1
    fi
    
    # Create backup first
    create_backup
    
    if [ "$BACKUP_ONLY" == "true" ]; then
        log_warning "Backup only mode, skipping deployment"
        send_telegram_notification "success" "Backup completed successfully"
        exit 0
    fi
    
    # Fetch latest changes from git
    log_info "Fetching latest changes from git..."
    git fetch origin main 2>/dev/null || git fetch origin master 2>/dev/null || {
        log_error "Failed to fetch from git"
        send_telegram_notification "failure" "Failed to fetch from git repository"
        exit 1
    }
    
    # Get current branch
    local current_branch=$(git rev-parse --abbrev-ref HEAD)
    log_info "Current branch: $current_branch"
    
    # Reset hard to latest
    log_info "Resetting to latest commit..."
    git reset --hard "origin/${current_branch}" 2>/dev/null || git reset --hard "origin/main" 2>/dev/null || {
        log_error "Failed to reset git"
        send_telegram_notification "failure" "Failed to reset git repository"
        exit 1
    }
    
    # Restore .env from backup if it exists in gitignore
    if [ -f "${BACKUP_DIR}/latest.env.bak" ] && [ ! -f ".env" ]; then
        log_info "Restoring .env from backup..."
        cp "${BACKUP_DIR}/latest.env.bak" .env
    fi
    
    # Install composer dependencies
    log_info "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist 2>&1 | while read line; do
        echo -e "\r${BLUE}[Composer]${NC} $line"
    done
    
    if [ $? -ne 0 ]; then
        log_error "Composer install failed"
        send_telegram_notification "failure" "Composer install failed"
        exit 1
    fi
    
    # Run database migrations
    if [ "$SKIP_MIGRATION" != "true" ]; then
        log_info "Running database migrations..."
        php artisan migrate --force --no-interaction
        
        if [ $? -ne 0 ]; then
            log_error "Database migration failed"
            send_telegram_notification "failure" "Database migration failed"
            exit 1
        fi
    else
        log_warning "Skipping database migrations as requested"
    fi
    
    # Clear all caches
    log_info "Clearing caches..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    
    # Rebuild caches
    log_info "Building production caches..."
    php artisan config:cache
    php artisan route:cache
    
    # Set proper permissions
    log_info "Setting file permissions..."
    chmod -R 755 storage bootstrap/cache 2>/dev/null || true
    chmod 644 .env 2>/dev/null || true
    
    # Optimize Laravel
    log_info "Optimizing Laravel..."
    php artisan optimize
    
    # Cleanup
    cleanup_logs
    
    # Final success message
    local deploy_time=$(date -u '+%Y-%m-%d %H:%M:%S UTC')
    local deploy_commit=$(git log -1 --pretty=format:'%h' 2>/dev/null || echo "unknown")
    
    log_success "Deployment completed successfully!"
    log_info "Commit: $deploy_commit"
    log_info "Time: $deploy_time"
    
    send_telegram_notification "success" "Deploy completed successfully!\nCommit: $deploy_commit"
}

# Verify deployment
verify_deployment() {
    log_info "Verifying deployment..."
    
    # Check if essential files exist
    if [ ! -f "public/index.php" ]; then
        log_error "public/index.php not found"
        return 1
    fi
    
    # Check if artisan works
    if ! php artisan --version > /dev/null 2>&1; then
        log_error "Artisan command failed"
        return 1
    fi
    
    # Check storage permissions
    if [ ! -w "storage" ]; then
        log_warning "storage directory is not writable"
    fi
    
    log_success "Deployment verification passed"
    return 0
}

# Main execution
main() {
    log_info "=========================================="
    log_info "  Vietnix Laravel Deployment Script"
    log_info "=========================================="
    
    # Display configuration
    log_info "Configuration:"
    log_info "  Deploy Path: $DEPLOY_PATH"
    log_info "  Skip Migration: $SKIP_MIGRATION"
    log_info "  Backup Only: $BACKUP_ONLY"
    log_info "  Keep Backups: $KEEP_BACKUPS"
    
    # Run deployment
    deploy
    
    # Verify deployment
    if verify_deployment; then
        log_success "All checks passed!"
    else
        log_warning "Some checks failed, please verify manually"
    fi
    
    log_info "Deployment finished at $(date)"
}

# Run main function
main "$@"

