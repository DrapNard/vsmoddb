import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

const Home = () => {
  const [featuredMods, setFeaturedMods] = useState([]);
  const [recentMods, setRecentMods] = useState([]);
  const [popularMods, setPopularMods] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchHomeData = async () => {
      try {
        setLoading(true);
        setError(null);
        
        // Fetch featured mods
        const featuredResponse = await axios.get('/mods?featured=true&limit=4');
        setFeaturedMods(featuredResponse.data.mods || []);
        
        // Fetch recent mods
        const recentResponse = await axios.get('/mods?sort=created_at&order=desc&limit=8');
        setRecentMods(recentResponse.data.mods || []);
        
        // Fetch popular mods
        const popularResponse = await axios.get('/mods?sort=downloads&order=desc&limit=8');
        setPopularMods(popularResponse.data.mods || []);
        
        setLoading(false);
      } catch (err) {
        console.error('Error fetching home data:', err);
        setError('Failed to load content. Please try again later.');
        setLoading(false);
      }
    };

    fetchHomeData();
  }, []);

  const renderModCard = (mod) => (
    <div key={mod.id} className="mod-card">
      <Link to={`/mods/${mod.id}`}>
        <div className="mod-image">
          <img src={mod.thumbnail_url || '/default-mod-image.png'} alt={mod.name} />
        </div>
        <div className="mod-info">
          <h3>{mod.name}</h3>
          <p className="mod-author">by {mod.author.username}</p>
          <p className="mod-description">{mod.short_description}</p>
          <div className="mod-meta">
            <span className="downloads">{mod.downloads} downloads</span>
          </div>
        </div>
      </Link>
    </div>
  );

  if (loading) {
    return <div className="loading">Loading...</div>;
  }

  if (error) {
    return <div className="error">{error}</div>;
  }

  return (
    <div className="home-container">
      <section className="hero-section">
        <div className="hero-content">
          <h1>Vintage Story Mod Database</h1>
          <p>Discover and download the best mods for Vintage Story</p>
          <div className="hero-buttons">
            <Link to="/mods" className="btn btn-primary">Browse Mods</Link>
            <Link to="/register" className="btn btn-secondary">Join Community</Link>
          </div>
        </div>
      </section>

      {featuredMods.length > 0 && (
        <section className="featured-section">
          <div className="section-header">
            <h2>Featured Mods</h2>
            <Link to="/mods?featured=true" className="view-all">View All</Link>
          </div>
          <div className="featured-grid">
            {featuredMods.map(renderModCard)}
          </div>
        </section>
      )}

      <section className="recent-section">
        <div className="section-header">
          <h2>Recently Added</h2>
          <Link to="/mods?sort=created_at&order=desc" className="view-all">View All</Link>
        </div>
        <div className="mods-grid">
          {recentMods.map(renderModCard)}
        </div>
      </section>

      <section className="popular-section">
        <div className="section-header">
          <h2>Most Popular</h2>
          <Link to="/mods?sort=downloads&order=desc" className="view-all">View All</Link>
        </div>
        <div className="mods-grid">
          {popularMods.map(renderModCard)}
        </div>
      </section>

      <section className="cta-section">
        <div className="cta-content">
          <h2>Create and Share Your Mods</h2>
          <p>Join our community of mod creators and share your work with Vintage Story players around the world.</p>
          <Link to="/register" className="btn btn-primary">Get Started</Link>
        </div>
      </section>
    </div>
  );
};

export default Home;