import React from 'react';
import { Link } from 'react-router-dom';

const Footer = () => {
  const currentYear = new Date().getFullYear();
  
  return (
    <footer className="footer">
      <div className="footer__container">
        <div className="footer__content">
          <div className="footer__section footer__section--about">
            <div className="footer__logo">
              <img src="/images/mod-default.png" alt="VSModDB Logo" />
            </div>
          </div>
          
          <div className="footer__section footer__section--links">
            <h3 className="footer__title">Quick Links</h3>
            <ul className="footer__links">
              <li><Link to="/" className="footer__link">Home</Link></li>
              <li><Link to="/mods" className="footer__link">Browse Mods</Link></li>
              <li><Link to="/login" className="footer__link">Login</Link></li>
              <li><Link to="/register" className="footer__link">Register</Link></li>
            </ul>
          </div>
          
          {/* Rest of the component remains the same */}
          <div className="footer__section footer__section--legal">
            <h3 className="footer__title">Legal</h3>
            <ul className="footer__links">
              <li><Link to="/terms" className="footer__link">Terms of Service</Link></li>
              <li><Link to="/privacy" className="footer__link">Privacy Policy</Link></li>
            </ul>
          </div>
        </div>
        
        <div className="footer__bottom">
          <p className="footer__copyright">
            &copy; {currentYear} VSModDB. All rights reserved.
          </p>
          <div className="footer__social">
            <a href="https://discord.gg/vintagestory" className="footer__social-link" target="_blank" rel="noopener noreferrer">
              <img src="/images/logo-discord.svg" alt="Discord" />
            </a>
            <a href="https://github.com/vintagestory" className="footer__social-link" target="_blank" rel="noopener noreferrer">
              <img src="/images/logo-github.svg" alt="GitHub" />
            </a>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;