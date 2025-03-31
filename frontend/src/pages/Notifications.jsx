import React from 'react';
import { Navigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import NotificationList from '../components/NotificationList';

const Notifications = () => {
  const { isAuthenticated } = useAuth();

  if (!isAuthenticated) {
    return <Navigate to="/login" />;
  }

  return (
    <div className="notifications-page">
      <div className="container">
        <NotificationList />
      </div>
    </div>
  );
};

export default Notifications;