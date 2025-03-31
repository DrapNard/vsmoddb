import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';

const UserProfile = () => {
  const { id } = useParams();
  const [user, setUser] = useState(null);
  const [userMods, setUserMods] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchUserData = async () => {
      try {
        setLoading(true);
        // Fetch user details
        const userResponse = await axios.get(`/users/${id}`);
        setUser(userResponse.data);
        
        // Fetch user's mods
        const modsResponse = await axios.get(`/users/${id}/mods`);
        setUserMods(modsResponse.data);
        
        setLoading(false);
      } catch (err) {
        setError('Failed to load user profile. Please try again later.');
        setLoading(false);
        console.error('Error fetching user profile:', err);
      }
    };

    fetchUserData();
  }, [id]);

  if (loading) {
    return (
      <div className="user-profile__loading">
        <p>Loading user profile...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="user-profile__error">
        <p>{error}</p>
        <Link to="/" className="btn btn-primary">Back to Home</Link>
      </div>
    );
  }

  if (!user) {
    return (
      <div className="user-profile__not-found">
        <h2>User Not Found</h2>
        <p>The user you're looking for doesn't exist or has been removed.</p>
        <Link to="/" className="btn btn-primary">Back to Home</Link>
      </div>
    );
  }

  return (
    <div className="user-profile">
      <div className="user-profile__header">
        <div className="user-profile__avatar">
          {user.avatar_url ? (
            <img src={user.avatar_url} alt={`${user.username}'s avatar`} />
          ) : (
            <div className="user-profile__avatar-placeholder">
              {user.username.charAt(0).toUpperCase()}
            </div>
          )}
        </div>
        
        <div className="user-profile__info">
          <h1 className="user-profile__username">{user.username}</h1>
          <div className="user-profile__meta">
            <span className="user-profile__joined">
              Joined {new Date(user.created_at).toLocaleDateString()}
            </span>
            <span className="user-profile__mods-count">
              {userMods.length} Mods
            </span>
          </div>
        </div>
      </div>

      {user.bio && (
        <div className="user-profile__bio">
          <h2>About</h2>
          <p>{user.bio}</p>
        </div>
      )}

      <div className="user-profile__mods">
        <h2>Mods by {user.username}</h2>
        
        {userMods.length === 0 ? (
          <p className="user-profile__no-mods">This user hasn't published any mods yet.</p>
        ) : (
          <div className="user-profile__mods-grid">
            {userMods.map(mod => (
              <div key={mod.id} className="mod-card">
                <div className="mod-card__image">
                  {mod.thumbnail_url ? (
                    <img src={mod.thumbnail_url} alt={mod.name} />
                  ) : (
                    <div className="mod-card__image-placeholder">No Image</div>
                  )}
                </div>
                
                <div className="mod-card__content">
                  <h3 className="mod-card__title">
                    <Link to={`/mods/${mod.id}`}>{mod.name}</Link>
                  </h3>
                  
                  <p className="mod-card__description">
                    {mod.short_description || mod.description.substring(0, 100) + '...'}
                  </p>
                  
                  <div className="mod-card__meta">
                    <span className="mod-card__downloads">{mod.downloads} Downloads</span>
                    <span className="mod-card__updated">
                      Updated {new Date(mod.updated_at).toLocaleDateString()}
                    </span>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default UserProfile;