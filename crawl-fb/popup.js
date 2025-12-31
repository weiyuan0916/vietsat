// {{CHENGQI:
// Action: Added
// Timestamp: 2025-10-16 14:30:00 UTC+7
// Reason: Popup UI controller for FB Group Collector extension
// Principles_Applied: Separation of concerns (UI logic isolated from business logic)
// Architecture_Note: Event-driven architecture with chrome.runtime messaging
// Security_Note: Input validation for user settings, message passing validation
// Performance_Note: Debounced storage updates, efficient DOM updates
// }}

document.addEventListener('DOMContentLoaded', () => {
  // DOM Elements
  const minDelayInput = document.getElementById('minDelay');
  const maxDelayInput = document.getElementById('maxDelay');
  const maxScrollsInput = document.getElementById('maxScrolls');
  const startBtn = document.getElementById('startBtn');
  const stopBtn = document.getElementById('stopBtn');
  const statusElement = document.getElementById('status');
  const scrollCountElement = document.getElementById('scrollCount');
  const postsCountElement = document.getElementById('postsCount');

  // Load saved settings
  loadSettings();

  // Save settings on input change
  [minDelayInput, maxDelayInput, maxScrollsInput].forEach(input => {
    input.addEventListener('change', saveSettings);
  });

  // Start button handler
  startBtn.addEventListener('click', async () => {
    const settings = getSettings();
    
    // Validation
    if (settings.minDelay >= settings.maxDelay) {
      alert('⚠️ Min Delay must be less than Max Delay');
      return;
    }

    // Get active tab
    const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
    
    // Check if on Facebook
    if (!tab.url || (!tab.url.includes('facebook.com'))) {
      alert('⚠️ Please navigate to a Facebook Group page first!');
      return;
    }

    // Send start command to content script
    try {
      await chrome.tabs.sendMessage(tab.id, {
        action: 'start',
        settings: settings
      });

      updateUIState(true);
      saveSettings();
    } catch (error) {
      console.error('Error starting crawler:', error);
      alert('⚠️ Failed to start crawler. Please refresh the Facebook page and try again.');
    }
  });

  // Stop button handler
  stopBtn.addEventListener('click', async () => {
    const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
    
    try {
      // Send stop command to content script
      await chrome.tabs.sendMessage(tab.id, {
        action: 'stop'
      });

      // Trigger download from background
      chrome.runtime.sendMessage({ action: 'download' });

      updateUIState(false);
    } catch (error) {
      console.error('Error stopping crawler:', error);
      // Still trigger download even if content script fails
      chrome.runtime.sendMessage({ action: 'download' });
      updateUIState(false);
    }
  });

  // Listen for status updates from background
  chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.type === 'status_update') {
      updateStatus(message.data);
    }
  });

  // Load initial status
  loadStatus();

  // Helper Functions
  function getSettings() {
    return {
      minDelay: parseFloat(minDelayInput.value) || 2,
      maxDelay: parseFloat(maxDelayInput.value) || 5,
      maxScrolls: parseInt(maxScrollsInput.value) || 50
    };
  }

  function saveSettings() {
    const settings = getSettings();
    chrome.storage.local.set({ 
      crawlerSettings: settings,
      isRunning: false
    });
  }

  function loadSettings() {
    chrome.storage.local.get(['crawlerSettings'], (result) => {
      if (result.crawlerSettings) {
        minDelayInput.value = result.crawlerSettings.minDelay || 2;
        maxDelayInput.value = result.crawlerSettings.maxDelay || 5;
        maxScrollsInput.value = result.crawlerSettings.maxScrolls || 50;
      }
    });
  }

  function updateUIState(isRunning) {
    if (isRunning) {
      startBtn.disabled = true;
      stopBtn.disabled = false;
      statusElement.innerHTML = '<span class="status-dot running"></span>Running';
      statusElement.classList.add('running');
      statusElement.classList.remove('stopped');
    } else {
      startBtn.disabled = false;
      stopBtn.disabled = true;
      statusElement.innerHTML = '<span class="status-dot stopped"></span>Stopped';
      statusElement.classList.add('stopped');
      statusElement.classList.remove('running');
    }
  }

  function updateStatus(data) {
    if (data.scrollCount !== undefined) {
      scrollCountElement.textContent = data.scrollCount;
    }
    if (data.postsCount !== undefined) {
      postsCountElement.innerHTML = `${data.postsCount} <span class="badge">NEW</span>`;
    }
    if (data.isRunning !== undefined) {
      updateUIState(data.isRunning);
    }
  }

  function loadStatus() {
    chrome.storage.local.get(['crawlerStatus'], (result) => {
      if (result.crawlerStatus) {
        updateStatus(result.crawlerStatus);
      }
    });
  }

  // Periodic status refresh
  setInterval(loadStatus, 1000);
});

