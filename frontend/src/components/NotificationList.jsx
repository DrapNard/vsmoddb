import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import { useAuth } from '../contexts/AuthContext';

const NotificationList = () => {
  const [notifications, setNotifications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const { isAuthenticated } = useAuth();

  useEffect(() => {
    if (!isAuthenticated) return;
    
    fetchNotifications();
  }, [isAuthenticated]);

  const fetchNotifications = async () => {
    try {
      setLoading(true);
      const response = await axios.get('/notifications');
      setNotifications(response.data);
      setLoading(false);
    } catch (err) {
      console.error('Error fetching notifications:', err);
      setError('Failed to load notifications. Please try again later.');
      setLoading(false);
    }
  };

  const markAsRead = async (notificationId) => {
    try {
      await axios.put(`/notifications/${notificationId}`, { read: true });
      
      // Update the notification in the list
      setNotifications(notifications.map(notification => 
        notification.id === notificationId ? { ...notification, read: true } : notification
      ));
    } catch (err) {
      console.error('Error marking notification as read:', err);
    }
  };

  const markAllAsRead = async () => {
    try {
      const unreadNotifications = notifications.filter(notification => !notification.read);
      
      // Mark each unread notification as read
      await Promise.all(unreadNotifications.map(notification => 
        axios.put(`/notifications/${notification.id}`, { read: true })
      ));
      
      // Update all notifications in the list
      setNotifications(notifications.map(notification => ({ ...notification, read: true })));
    } catch (err) {
      console.error('Error marking all notifications as read:', err);
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleString();
  };

  if (!isAuthenticated) {
    return null;
  }

  if (loading) {
    return <div className="notifications__loading">Loading notifications...</div>;
  }

  if (error) {
    return <div className="notifications__error">{error}</div>;
  }

  return (
    <div className="notifications">
      <div className="notifications__header">
        <h2>Notifications</h2>
        {notifications.some(notification => !notification.read) && (
          <button 
            className="notifications__mark-all-btn"
            onClick={markAllAsRead}
          >
            Mark all as read
          </button>
        )}
      </div>

      {notifications.length === 0 ? (
        <p className="notifications__empty">You have no notifications.</p>
      ) : (
        <ul className="notifications__list">
          {notifications.map(notification => (
            <li 
              key={notification.id} 
              className={`notification ${notification.read ? 'notification--read' : 'notification--unread'}`}
              onClick={() => !notification.read && markAsRead(notification.id)}
            >
              <div className="notification__content">
                <div className="notification__message">{notification.message}</div>
                <div className="notification__meta">
                  <span className="notification__time">{formatDate(notification.created)}</span>
                  {notification.assetid && (
                    <Link 
                      to={`/${notification.assettype.toLowerCase()}s/${notification.assetid}`}
                      className="notification__link"
                    >
                      View
                    </Link>
                  )}
                </div>
              </div>
              {!notification.read && (
                <div className="notification__indicator"></div>
              )}
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default NotificationList;