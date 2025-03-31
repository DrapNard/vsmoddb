import React from 'react';
import { Link, NavLink, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

const Header = () => {
  const { currentUser, isAuthenticated, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/');
  };

  return (
    <header className="header">
      <div className="header__container">
        <div className="header__logo">
          <Link to="/" className="header__logo-link">
            <img src="/images/vsmoddb-logo.png" alt="VSModDB" />
          </Link>
        </div>
        
        <nav className="header__nav">
          <ul className="header__nav-list">
            <li className="header__nav-item">
              <NavLink to="/" className={({ isActive }) => 
                isActive ? "header__nav-link header__nav-link--active" : "header__nav-link"
              }>
                Home
              </NavLink>
            </li>
            <li className="header__nav-item">
              <NavLink to="/mods" className={({ isActive }) => 
                isActive ? "header__nav-link header__nav-link--active" : "header__nav-link"
              }>
                Mods
              </NavLink>
            </li>
          </ul>
        </nav>
        
        <div className="header__auth">
          {isAuthenticated ? (
            <div className="header__user-menu">
              <div className="header__user-info">
                <span className="header__username">{currentUser.username}</span>
              </div>
              <div className="header__dropdown">
                <Link to="/profile" className="header__dropdown-item">My Profile</Link>
                <button onClick={handleLogout} className="header__dropdown-item header__dropdown-item--button">
                  Logout
                </button>
              </div>
            </div>
          ) : (
            <div className="header__auth-buttons">
              <Link to="/login" className="btn btn-outline">Log In</Link>
              <Link to="/register" className="btn btn-primary">Sign Up</Link>
            </div>
          )}
        </div>
      </div>
    </header>
  );
};

export default Header;