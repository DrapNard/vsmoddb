import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';

const ModDetail = () => {
  const { id } = useParams();
  const [mod, setMod] = useState(null);
  const [releases, setReleases] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchModData = async () => {
      try {
        setLoading(true);
        // Fetch mod details
        const modResponse = await axios.get(`/mods/${id}`);
        setMod(modResponse.data);
        
        // Fetch mod releases
        const releasesResponse = await axios.get(`/mods/${id}/releases`);
        setReleases(releasesResponse.data);
        
        setLoading(false);
      } catch (err) {
        setError('Failed to load mod details. Please try again later.');
        setLoading(false);
        console.error('Error fetching mod details:', err);
      }
    };

    fetchModData();
  }, [id]);

  if (loading) {
    return (
      <div className="mod-detail__loading">
        <p>Loading mod details...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="mod-detail__error">
        <p>{error}</p>
        <Link to="/mods" className="btn btn-primary">Back to Mods</Link>
      </div>
    );
  }

  if (!mod) {
    return (
      <div className="mod-detail__not-found">
        <h2>Mod Not Found</h2>
        <p>The mod you're looking for doesn't exist or has been removed.</p>
        <Link to="/mods" className="btn btn-primary">Back to Mods</Link>
      </div>
    );
  }

  return (
    <div className="mod-detail">
      <div className="mod-detail__header">
        <h1 className="mod-detail__title">{mod.name}</h1>
        <div className="mod-detail__meta">
          <span className="mod-detail__author">
            By <Link to={`/users/${mod.author.id}`}>{mod.author.username}</Link>
          </span>
          <span className="mod-detail__category">{mod.category}</span>
          <span className="mod-detail__downloads">{mod.downloads} Downloads</span>
        </div>
      </div>

      <div className="mod-detail__content">
        <div className="mod-detail__main">
          {mod.images && mod.images.length > 0 && (
            <div className="mod-detail__gallery">
              <img 
                src={mod.images[0].url} 
                alt={mod.name} 
                className="mod-detail__main-image" 
              />
              {mod.images.length > 1 && (
                <div className="mod-detail__thumbnails">
                  {mod.images.map((image, index) => (
                    <img 
                      key={index}
                      src={image.url} 
                      alt={`${mod.name} screenshot ${index + 1}`} 
                      className="mod-detail__thumbnail" 
                    />
                  ))}
                </div>
              )}
            </div>
          )}

          <div className="mod-detail__description">
            <h2>Description</h2>
            <div dangerouslySetInnerHTML={{ __html: mod.description }} />
          </div>

          {mod.tags && mod.tags.length > 0 && (
            <div className="mod-detail__tags">
              <h3>Tags</h3>
              <div className="mod-detail__tags-list">
                {mod.tags.map(tag => (
                  <Link 
                    key={tag.id} 
                    to={`/mods?tag=${tag.name}`} 
                    className="mod-detail__tag"
                  >
                    {tag.name}
                  </Link>
                ))}
              </div>
            </div>
          )}
        </div>

        <div className="mod-detail__sidebar">
          <div className="mod-detail__actions">
            {releases.length > 0 && (
              <Link 
                to={`/releases/${releases[0].id}`} 
                className="btn btn-primary btn-block"
              >
                Download Latest Version
              </Link>
            )}
          </div>

          <div className="mod-detail__info-card">
            <h3>Information</h3>
            <ul className="mod-detail__info-list">
              <li><strong>Created:</strong> {new Date(mod.created_at).toLocaleDateString()}</li>
              <li><strong>Updated:</strong> {new Date(mod.updated_at).toLocaleDateString()}</li>
              <li><strong>Game Version:</strong> {mod.game_version}</li>
              <li><strong>License:</strong> {mod.license}</li>
            </ul>
          </div>

          {releases.length > 0 && (
            <div className="mod-detail__releases">
              <h3>Releases</h3>
              <ul className="mod-detail__releases-list">
                {releases.map(release => (
                  <li key={release.id} className="mod-detail__release-item">
                    <Link to={`/releases/${release.id}`} className="mod-detail__release-link">
                      <span className="mod-detail__release-version">{release.version}</span>
                      <span className="mod-detail__release-date">
                        {new Date(release.created_at).toLocaleDateString()}
                      </span>
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default ModDetail;