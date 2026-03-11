# ============================================
# Makefile for Running All Projects
# - api_tpbank_free (Node.js API)
# - yiki (Framework7 PWA)
# - pwa-ecommerce (Laravel + Queue)
# ============================================

# Colors for output
GREEN = \033[0;32m
YELLOW = \033[1;33m
BLUE = \033[0;34m
RED = \033[0;31m
NC = \033[0m # No Color

# Project paths
API_TPBANK_PATH = $(HOME)/Herd/vietsat/api_tpbank_free
YIKI_PATH = $(HOME)/Herd/vietsat/yiki
PWA_ECOMMERCE_PATH = $(HOME)/Herd/vietsat/pwa-ecommerce

# Default ports
YIKI_PORT ?= 3002
PWA_PORT ?= 8000
TPBANK_PORT ?= 3005

# Help target
.PHONY: help
help:
	@echo ""
	@echo -e "$(BLUE)╔════════════════════════════════════════════════════════════╗$(NC)"
	@echo -e "$(BLUE)║         VIETSAT PROJECTS - RUN ALL SERVICES                 ║$(NC)"
	@echo -e "$(BLUE)╚════════════════════════════════════════════════════════════╝$(NC)"
	@echo ""
	@echo -e "$(YELLOW)🚀 RUN ALL PROJECTS$(NC)"
	@echo "   make run              - Run all 3 projects in background"
	@echo "   make run-foreground   - Run all 3 projects in foreground (Ctrl+C to stop all)"
	@echo ""
	@echo -e "$(YELLOW)📦 INDIVIDUAL PROJECTS$(NC)"
	@echo "   make tpbank           - Run api_tpbank_free (Node.js)"
	@echo "   make yiki             - Run yiki (Framework7 PWA)"
	@echo "   make queue            - Run Laravel queue (pwa-ecommerce)"
	@echo "   make serve            - Run Laravel server (pwa-ecommerce)"
	@echo ""
	@echo -e "$(YELLOW)🛑 STOP PROJECTS$(NC)"
	@echo "   make stop             - Stop all running projects"
	@echo "   make stop-tpbank      - Stop api_tpbank_free"
	@echo "   make stop-yiki        - Stop yiki"
	@echo "   make stop-queue       - Stop Laravel queue"
	@echo "   make stop-serve       - Stop Laravel server"
	@echo ""
	@echo -e "$(YELLOW)📋 STATUS$(NC)"
	@echo "   make status           - Check running projects"
	@echo ""
	@echo -e "$(YELLOW)💡 CUSTOM PORTS$(NC)"
	@echo "   make run YIKI_PORT=8080 TPBANK_PORT=3002"
	@echo ""
	@echo -e "$(GREEN)Happy coding! 🚀$(NC)"
	@echo ""

# ============================================
# RUN ALL PROJECTS (Background)
# ============================================

.PHONY: run
run: check-installed
	@echo -e "$(GREEN)🚀 Starting all projects in background...$(NC)"
	@echo ""
	@echo -e "$(BLUE)📦 api_tpbank_free$(NC)  - http://localhost:$(TPBANK_PORT)"
	@echo -e "$(BLUE)📱 yiki$(NC)              - http://localhost:$(YIKI_PORT)"
	@echo -e "$(BLUE)⚙️  pwa-ecommerce queue$(NC)"
	@echo ""
	
	# Start api_tpbank_free
	@cd $(API_TPBANK_PATH) && nohup npm start > /tmp/tpbank.log 2>&1 &
	@echo $$! > /tmp/tpbank.pid
	@sleep 1
	
	# Start yiki
	@cd $(YIKI_PATH) && nohup npx serve . -l $(YIKI_PORT) > /tmp/yiki.log 2>&1 &
	@echo $$! > /tmp/yiki.pid
	@sleep 1
	
	# Start Laravel queue
	@cd $(PWA_ECOMMERCE_PATH) && nohup php artisan queue:work --sleep=3 --tries=3 --max-time=3600 > /tmp/queue.log 2>&1 &
	@echo $$! > /tmp/queue.pid
	
	@echo -e "$(GREEN)✅ All projects started!$(NC)"
	@echo -e "$(YELLOW)Logs location:$(NC)"
	@echo "   - api_tpbank_free: /tmp/tpbank.log"
	@echo "   - yiki: /tmp/yiki.log"
	@echo "   - queue: /tmp/queue.log"
	@echo ""
	@echo -e "$(GREEN)Use 'make status' to check running projects$(NC)"

# ============================================
# RUN ALL PROJECTS (Foreground)
# ============================================

.PHONY: run-foreground
run-foreground: check-installed
	@echo -e "$(GREEN)🚀 Starting all projects in foreground...$(NC)"
	@echo -e "$(YELLOW)Press Ctrl+C to stop all projects$(NC)"
	@echo ""
	
	@cd $(API_TPBANK_PATH) && npm start &
	@cd $(YIKI_PATH) && npx serve . -l $(YIKI_PORT) &
	@cd $(PWA_ECOMMERCE_PATH) && php artisan queue:work --sleep=3 --tries=3 --max-time=3600
	
	@wait

# ============================================
# INDIVIDUAL PROJECTS
# ============================================

# api_tpbank_free
.PHONY: tpbank
tpbank:
	@echo -e "$(GREEN)🚀 Starting api_tpbank_free on port $(TPBANK_PORT)...$(NC)"
	@cd $(API_TPBANK_PATH) && npm start

# yiki
.PHONY: yiki
yiki:
	@echo -e "$(GREEN)🚀 Starting yiki on port $(YIKI_PORT)...$(NC)"
	@cd $(YIKI_PATH) && npx serve . -l $(YIKI_PORT)

# Laravel queue
.PHONY: queue
queue:
	@echo -e "$(GREEN)⚙️  Starting Laravel queue worker...$(NC)"
	@cd $(PWA_ECOMMERCE_PATH) && php artisan queue:work --sleep=3 --tries=3 --max-time=3600

# Laravel server
.PHONY: serve
serve:
	@echo -e "$(GREEN)🚀 Starting Laravel server on port $(PWA_PORT)...$(NC)"
	@cd $(PWA_ECOMMERCE_PATH) && php artisan serve --port=$(PWA_PORT)

# ============================================
# BACKGROUND VERSIONS (Individual)
# ============================================

.PHONY: tpbank-bg
tpbank-bg:
	@echo -e "$(GREEN)🚀 Starting api_tpbank_free in background...$(NC)"
	@cd $(API_TPBANK_PATH) && nohup npm start > /tmp/tpbank.log 2>&1 &
	@echo $$! > /tmp/tpbank.pid
	@sleep 1
	@echo -e "$(GREEN)✅ api_tpbank_free started (check /tmp/tpbank.log)$(NC)"

.PHONY: yiki-bg
yiki-bg:
	@echo -e "$(GREEN)🚀 Starting yiki in background...$(NC)"
	@cd $(YIKI_PATH) && nohup npx serve . -l $(YIKI_PORT) > /tmp/yiki.log 2>&1 &
	@echo $$! > /tmp/yiki.pid
	@sleep 1
	@echo -e "$(GREEN)✅ yiki started (check /tmp/yiki.log)$(NC)"

.PHONY: queue-bg
queue-bg:
	@echo -e "$(GREEN)⚙️  Starting Laravel queue in background...$(NC)"
	@cd $(PWA_ECOMMERCE_PATH) && nohup php artisan queue:work --sleep=3 --tries=3 --max-time=3600 > /tmp/queue.log 2>&1 &
	@echo $$! > /tmp/queue.pid
	@echo -e "$(GREEN)✅ Queue started (check /tmp/queue.log)$(NC)"

.PHONY: serve-bg
serve-bg:
	@echo -e "$(GREEN)🚀 Starting Laravel server in background...$(NC)"
	@cd $(PWA_ECOMMERCE_PATH) && nohup php artisan serve --port=$(PWA_PORT) > /tmp/serve.log 2>&1 &
	@echo $$! > /tmp/serve.pid
	@echo -e "$(GREEN)✅ Laravel server started (check /tmp/serve.log)$(NC)"

# ============================================
# STOP PROJECTS
# ============================================

.PHONY: stop
stop:
	@echo -e "$(YELLOW)🛑 Stopping all projects...$(NC)"
	@make stop-tpbank
	@make stop-yiki
	@make stop-queue
	@make stop-serve
	@echo -e "$(GREEN)✅ All projects stopped$(NC)"

.PHONY: stop-tpbank
stop-tpbank:
	@if [ -f /tmp/tpbank.pid ]; then \
		pid=$$(cat /tmp/tpbank.pid); \
		kill $$pid 2>/dev/null; \
		rm /tmp/tpbank.pid; \
		echo -e "$(GREEN)✅ api_tpbank_free stopped$(NC)"; \
	else \
		pkill -f "nodemon index.js" 2>/dev/null || true; \
		pkill -f "node.*index.js" 2>/dev/null || true; \
		echo -e "$(YELLOW)⚠️  api_tpbank_free was not running$(NC)"; \
	fi

.PHONY: stop-yiki
stop-yiki:
	@if [ -f /tmp/yiki.pid ]; then \
		pid=$$(cat /tmp/yiki.pid); \
		kill $$pid 2>/dev/null; \
		rm /tmp/yiki.pid; \
		echo -e "$(GREEN)✅ yiki stopped$(NC)"; \
	else \
		pkill -f "serve.*$(YIKI_PORT)" 2>/dev/null || true; \
		echo -e "$(YELLOW)⚠️  yiki was not running$(NC)"; \
	fi

.PHONY: stop-queue
stop-queue:
	@if [ -f /tmp/queue.pid ]; then \
		pid=$$(cat /tmp/queue.pid); \
		kill $$pid 2>/dev/null; \
		rm /tmp/queue.pid; \
		echo -e "$(GREEN)✅ Laravel queue stopped$(NC)"; \
	else \
		pkill -f "queue:work" 2>/dev/null || true; \
		echo -e "$(YELLOW)⚠️  Queue was not running$(NC)"; \
	fi

.PHONY: stop-serve
stop-serve:
	@if [ -f /tmp/serve.pid ]; then \
		pid=$$(cat /tmp/serve.pid); \
		kill $$pid 2>/dev/null; \
		rm /tmp/serve.pid; \
		echo -e "$(GREEN)✅ Laravel server stopped$(NC)"; \
	else \
		pkill -f "artisan serve" 2>/dev/null || true; \
		echo -e "$(YELLOW)⚠️  Laravel server was not running$(NC)"; \
	fi

# ============================================
# STATUS
# ============================================

.PHONY: status
status:
	@echo ""
	@echo -e "$(BLUE)╔════════════════════════════════════════════════════════════╗$(NC)"
	@echo -e "$(BLUE)║                    PROJECT STATUS                          ║$(NC)"
	@echo -e "$(BLUE)╚════════════════════════════════════════════════════════════╝$(NC)"
	@echo ""
	
	@echo -n "api_tpbank_free: "
	@if pgrep -f "nodemon index.js" > /dev/null || pgrep -f "node.*index.js" > /dev/null; then \
		echo -e "$(GREEN)Running$(NC)"; \
	else \
		echo -e "$(RED)Stopped$(NC)"; \
	fi
	
	@echo -n "yiki: "
	@if pgrep -f "serve.*$(YIKI_PORT)" > /dev/null || pgrep -f "serve\.js" > /dev/null; then \
		echo -e "$(GREEN)Running on port $(YIKI_PORT)$(NC)"; \
	else \
		echo -e "$(RED)Stopped$(NC)"; \
	fi
	
	@echo -n "Laravel Queue: "
	@if pgrep -f "queue:work" > /dev/null; then \
		echo -e "$(GREEN)Running$(NC)"; \
	else \
		echo -e "$(RED)Stopped$(NC)"; \
	fi
	
	@echo -n "Laravel Server: "
	@if pgrep -f "artisan serve" > /dev/null; then \
		echo -e "$(GREEN)Running on port $(PWA_PORT)$(NC)"; \
	else \
		echo -e "$(RED)Stopped$(NC)"; \
	fi
	@echo ""

# ============================================
# LOGS
# ============================================

.PHONY: logs
logs:
	@echo -e "$(BLUE)=== api_tpbank_free logs ===$(NC)"
	@tail -n 20 /tmp/tpbank.log 2>/dev/null || echo "No logs found"
	@echo ""
	@echo -e "$(BLUE)=== yiki logs ===$(NC)"
	@tail -n 20 /tmp/yiki.log 2>/dev/null || echo "No logs found"
	@echo ""
	@echo -e "$(BLUE)=== queue logs ===$(NC)"
	@tail -n 20 /tmp/queue.log 2>/dev/null || echo "No logs found"
	@echo ""
	@echo -e "$(BLUE)=== serve logs ===$(NC)"
	@tail -n 20 /tmp/serve.log 2>/dev/null || echo "No logs found"

.PHONY: logs-tpbank
logs-tpbank:
	@tail -f /tmp/tpbank.log

.PHONY: logs-yiki
logs-yiki:
	@tail -f /tmp/yiki.log

.PHONY: logs-queue
logs-queue:
	@tail -f /tmp/queue.log

.PHONY: logs-serve
logs-serve:
	@tail -f /tmp/serve.log

# ============================================
# CHECK INSTALLED
# ============================================

.PHONY: check-installed
check-installed:
	@echo -e "$(YELLOW)Checking dependencies...$(NC)"
	
	# Check node_modules for api_tpbank_free
	@if [ ! -d "$(API_TPBANK_PATH)/node_modules" ]; then \
		echo -e "$(YELLOW)⚠️  api_tpbank_free: node_modules not found. Installing...$(NC)"; \
		cd $(API_TPBANK_PATH) && npm install; \
	fi
	
	# Check node_modules for yiki
	@if [ ! -d "$(YIKI_PATH)/node_modules" ]; then \
		echo -e "$(YELLOW)⚠️  yiki: node_modules not found. Installing...$(NC)"; \
		cd $(YIKI_PATH) && npm install; \
	fi
	
	@echo -e "$(GREEN)✅ All dependencies ready$(NC)"
	@echo ""

# ============================================
# RESTART
# ============================================

.PHONY: restart
restart:
	@echo -e "$(YELLOW)🔄 Restarting all projects...$(NC)"
	@make stop
	@sleep 2
	@make run

.PHONY: restart-tpbank
restart-tpbank:
	@make stop-tpbank
	@sleep 1
	@make tpbank-bg

.PHONY: restart-yiki
restart-yiki:
	@make stop-yiki
	@sleep 1
	@make yiki-bg

.PHONY: restart-queue
restart-queue:
	@make stop-queue
	@sleep 1
	@make queue-bg

.PHONY: restart-serve
restart-serve:
	@make stop-serve
	@sleep 1
	@make serve-bg

# ============================================
# INSTALL
# ============================================

.PHONY: install
install:
	@echo -e "$(GREEN)📦 Installing all dependencies...$(NC)"
	@echo ""
	@echo -e "$(BLUE)Installing api_tpbank_free...$(NC)"
	@cd $(API_TPBANK_PATH) && npm install
	@echo ""
	@echo -e "$(BLUE)Installing yiki...$(NC)"
	@cd $(YIKI_PATH) && npm install
	@echo ""
	@echo -e "$(GREEN)✅ All dependencies installed!$(NC)"

# ============================================
# QUICK ALIAS
# ============================================

.PHONY: s
s: run

.PHONY: ss
ss: stop

.PHONY: st
st: status

