class NotificationComponent {
    constructor(options = {}) {
        this.apiUrl = options.apiUrl || './api/notifications_api.php';
        this.userId = options.userId || null;
        this.userType = options.userType || 'user';
        this.pollInterval = options.pollInterval || 30000; // 30 seconds
        this.container = null;
        this.notifications = [];
        this.unreadCount = 0;
        
        this.init();
    }

    init() {
        this.createNotificationContainer();
        this.loadNotifications();
        this.setupPolling();
        this.setupEventListeners();
    }

    createNotificationContainer() {
        // Create notification bell icon
        const bellHTML = `
            <div class="notification-bell" id="notification-bell">
                <i class="fas fa-bell"></i>
                <span class="notification-count" id="notification-count">0</span>
            </div>
        `;

        // Create notification dropdown
        const dropdownHTML = `
            <div class="notification-dropdown" id="notification-dropdown">
                <div class="notification-header">
                    <h3>Notifications</h3>
                    <button class="mark-all-read" id="mark-all-read">Mark all read</button>
                </div>
                <div class="notification-list" id="notification-list">
                    <div class="notification-loading">Loading notifications...</div>
                </div>
            </div>
        `;

        // Insert into navbar
        const userActions = document.querySelector('.user-actions');
        if (userActions) {
            userActions.insertAdjacentHTML('beforeend', bellHTML);
            userActions.insertAdjacentHTML('beforeend', dropdownHTML);
        }

        this.addStyles();
    }

    addStyles() {
        const styles = `
            <style>
                .notification-bell {
                    position: relative;
                    cursor: pointer;
                    padding: 8px;
                    border-radius: 50%;
                    transition: all 0.3s ease;
                    color: #333;
                    font-size: 18px;
                }

                .notification-bell:hover {
                    background: rgba(17, 182, 113, 0.1);
                    color: #11b671;
                }

                .notification-count {
                    position: absolute;
                    top: 0;
                    right: 0;
                    background: #ff4757;
                    color: white;
                    border-radius: 50%;
                    width: 18px;
                    height: 18px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 10px;
                    font-weight: bold;
                    transform: scale(0);
                    transition: transform 0.3s ease;
                }

                .notification-count.show {
                    transform: scale(1);
                }

                .notification-dropdown {
                    position: absolute;
                    top: 100%;
                    right: 0;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
                    border: 1px solid rgba(17, 182, 113, 0.1);
                    min-width: 350px;
                    max-width: 400px;
                    max-height: 500px;
                    opacity: 0;
                    visibility: hidden;
                    transform: translateY(-10px);
                    transition: all 0.3s ease;
                    z-index: 1000;
                }

                .notification-dropdown.show {
                    opacity: 1;
                    visibility: visible;
                    transform: translateY(0);
                }

                .notification-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 16px;
                    border-bottom: 1px solid #f0f0f0;
                    background: rgba(17, 182, 113, 0.05);
                }

                .notification-header h3 {
                    margin: 0;
                    color: #11b671;
                    font-size: 16px;
                    font-weight: 600;
                }

                .mark-all-read {
                    background: none;
                    border: none;
                    color: #11b671;
                    font-size: 12px;
                    cursor: pointer;
                    padding: 4px 8px;
                    border-radius: 4px;
                    transition: background 0.3s ease;
                }

                .mark-all-read:hover {
                    background: rgba(17, 182, 113, 0.1);
                }

                .notification-list {
                    max-height: 400px;
                    overflow-y: auto;
                }

                .notification-item {
                    padding: 12px 16px;
                    border-bottom: 1px solid #f0f0f0;
                    cursor: pointer;
                    transition: background 0.3s ease;
                    position: relative;
                }

                .notification-item:hover {
                    background: rgba(17, 182, 113, 0.05);
                }

                .notification-item.unread {
                    background: rgba(17, 182, 113, 0.08);
                    border-left: 3px solid #11b671;
                }

                .notification-item.unread::before {
                    content: '';
                    position: absolute;
                    top: 50%;
                    right: 12px;
                    width: 8px;
                    height: 8px;
                    background: #11b671;
                    border-radius: 50%;
                    transform: translateY(-50%);
                }

                .notification-title {
                    font-weight: 600;
                    color: #333;
                    margin-bottom: 4px;
                    font-size: 14px;
                }

                .notification-message {
                    color: #666;
                    font-size: 13px;
                    line-height: 1.4;
                    margin-bottom: 4px;
                }

                .notification-time {
                    color: #999;
                    font-size: 11px;
                }

                .notification-type {
                    display: inline-block;
                    padding: 2px 6px;
                    border-radius: 4px;
                    font-size: 10px;
                    font-weight: 500;
                    text-transform: uppercase;
                    margin-right: 8px;
                }

                .notification-type.success {
                    background: #d1fae5;
                    color: #065f46;
                }

                .notification-type.info {
                    background: #dbeafe;
                    color: #1e40af;
                }

                .notification-type.warning {
                    background: #fef3c7;
                    color: #92400e;
                }

                .notification-type.error {
                    background: #fee2e2;
                    color: #991b1b;
                }

                .notification-loading {
                    padding: 20px;
                    text-align: center;
                    color: #666;
                }

                .notification-empty {
                    padding: 20px;
                    text-align: center;
                    color: #666;
                }

                @media (max-width: 768px) {
                    .notification-dropdown {
                        min-width: 300px;
                        max-width: 90vw;
                    }
                }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    setupEventListeners() {
        const bell = document.getElementById('notification-bell');
        const dropdown = document.getElementById('notification-dropdown');
        const markAllRead = document.getElementById('mark-all-read');

        if (bell) {
            bell.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });
        }

        if (markAllRead) {
            markAllRead.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    }

    async loadNotifications() {
        if (!this.userId) return;

        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get_notifications',
                    user_id: this.userId,
                    user_type: this.userType
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.notifications = data.notifications;
                this.updateUnreadCount();
                this.renderNotifications();
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    updateUnreadCount() {
        this.unreadCount = this.notifications.filter(n => !n.is_read).length;
        const countElement = document.getElementById('notification-count');
        
        if (countElement) {
            if (this.unreadCount > 0) {
                countElement.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                countElement.classList.add('show');
            } else {
                countElement.classList.remove('show');
            }
        }
    }

    renderNotifications() {
        const list = document.getElementById('notification-list');
        
        if (!list) return;

        if (this.notifications.length === 0) {
            list.innerHTML = '<div class="notification-empty">No notifications yet</div>';
            return;
        }

        const notificationsHTML = this.notifications.map(notification => `
            <div class="notification-item ${!notification.is_read ? 'unread' : ''}" 
                 data-id="${notification.id}" 
                 onclick="notificationComponent.markAsRead(${notification.id})">
                <div class="notification-title">
                    <span class="notification-type ${notification.type}">${notification.type}</span>
                    ${notification.title}
                </div>
                <div class="notification-message">${notification.message}</div>
                <div class="notification-time">${this.formatTime(notification.created_at)}</div>
            </div>
        `).join('');

        list.innerHTML = notificationsHTML;
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'mark_read',
                    notification_id: notificationId
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Update local notifications
                const notification = this.notifications.find(n => n.id == notificationId);
                if (notification) {
                    notification.is_read = 1;
                    this.updateUnreadCount();
                    this.renderNotifications();
                }
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    async markAllAsRead() {
        if (!this.userId) return;

        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'mark_all_read',
                    user_id: this.userId,
                    user_type: this.userType
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Update local notifications
                this.notifications.forEach(n => n.is_read = 1);
                this.updateUnreadCount();
                this.renderNotifications();
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }

    setupPolling() {
        setInterval(() => {
            this.loadNotifications();
        }, this.pollInterval);
    }

    formatTime(timestamp) {
        const now = new Date();
        const time = new Date(timestamp);
        const diff = now - time;
        
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);
        
        if (minutes < 1) return 'Just now';
        if (minutes < 60) return `${minutes}m ago`;
        if (hours < 24) return `${hours}h ago`;
        if (days < 7) return `${days}d ago`;
        
        return time.toLocaleDateString();
    }

    setUserId(userId) {
        this.userId = userId;
        this.loadNotifications();
    }

    setUserType(userType) {
        this.userType = userType;
        this.loadNotifications();
    }
}

// Initialize notification component
document.addEventListener('DOMContentLoaded', function() {
    window.notificationComponent = new NotificationComponent({
        userId: localStorage.getItem('medimerge_user_id') || sessionStorage.getItem('medimerge_user_id'),
        userType: 'user'
    });
});
