import React, { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import axios from 'axios';

const Profile = () => {
  const { currentUser, updateProfile, error } = useAuth();
  const [userMods, setUserMods] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('profile');
  const [formData, setFormData] = useState({
    username: '',
    email: '',
    bio: '',
    website: '',
    discord: '',
    github: ''
  });
  const [avatar, setAvatar] = useState(null);
  const [avatarPreview, setAvatarPreview] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [formError, setFormError] = useState('');
  const [formSuccess, setFormSuccess] = useState('');

  // Load user data
  useEffect(() => {
    if (currentUser) {
      setFormData({
        username: currentUser.username || '',
        email: currentUser.email || '',
        bio: currentUser.bio || '',
        website: currentUser.website || '',
        discord: currentUser.discord || '',
        github: currentUser.github || ''
      });
      setAvatarPreview(currentUser.avatar_url || '');
    }
  }, [currentUser]);

  // Fetch user's mods
  useEffect(() => {
    const fetchUserMods = async () => {
      if (!currentUser) return;
      
      try {
        setLoading(true);
        const response = await axios.get(`/users/${currentUser.id}/mods`);
        setUserMods(response.data.mods || []);
        setLoading(false);
      } catch (err) {
        console.error('Error fetching user mods:', err);
        setLoading(false);
      }
    };

    fetchUserMods();
  }, [currentUser]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleAvatarChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setAvatar(file);
      const reader = new FileReader();
      reader.onloadend = () => {
        setAvatarPreview(reader.result);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setFormError('');
    setFormSuccess('');
    setIsSubmitting(true);

    try {
      // Create form data for multipart/form-data
      const profileData = new FormData();
      
      // Add text fields
      Object.keys(formData).forEach(key => {
        profileData.append(key, formData[key]);
      });
      
      // Add avatar if changed
      if (avatar) {
        profileData.append('avatar', avatar);
      }
      
      const success = await updateProfile(profileData);
      
      if (success) {
        setFormSuccess('Profile updated successfully!');
        // Reset avatar state
        setAvatar(null);
      }
    } catch (err) {
      setFormError('Failed to update profile. Please try again.');
      console.error('Profile update error:', err);
    } finally {
      setIsSubmitting(false);
    }
  };

  if (!currentUser) {
    return <div className="loading">Loading profile...</div>;
  }

  return (
    <div className="profile-container">
      <div className="profile-header">
        <div className="profile-avatar">
          <img src={avatarPreview || '/default-avatar.png'} alt={currentUser.username} />
        </div>
        <div className="profile-info">
          <h1>{currentUser.username}</h1>
          <p className="member-since">Member since {new Date(currentUser.created_at).toLocaleDateString()}</p>
        </div>
      </div>

      <div className="profile-tabs">
        <button 
          className={`tab-button ${activeTab === 'profile' ? 'active' : ''}`}
          onClick={() => setActiveTab('profile')}
        >
          Profile Settings
        </button>
        <button 
          className={`tab-button ${activeTab === 'mods' ? 'active' : ''}`}
          onClick={() => setActiveTab('mods')}
        >
          My Mods
        </button>
        <button 
          className={`tab-button ${activeTab === 'security' ? 'active' : ''}`}
          onClick={() => setActiveTab('security')}
        >
          Security
        </button>
      </div>

      <div className="profile-content">
        {activeTab === 'profile' && (
          <div className="profile-settings">
            <h2>Edit Profile</h2>
            
            {formError && <div className="error-message">{formError}</div>}
            {formSuccess && <div className="success-message">{formSuccess}</div>}
            {error && <div className="error-message">{error}</div>}
            
            <form onSubmit={handleSubmit} className="profile-form">
              <div className="form-group">
                <label htmlFor="avatar">Profile Picture</label>
                <div className="avatar-upload">
                  <img 
                    src={avatarPreview || '/default-avatar.png'} 
                    alt="Avatar preview" 
                    className="avatar-preview"
                  />
                  <input 
                    type="file" 
                    id="avatar" 
                    accept="image/*"
                    onChange={handleAvatarChange}
                  />
                </div>
              </div>
              
              <div className="form-group">
                <label htmlFor="username">Username</label>
                <input
                  type="text"
                  id="username"
                  name="username"
                  value={formData.username}
                  onChange={handleChange}
                  required
                />
              </div>
              
              <div className="form-group">
                <label htmlFor="email">Email</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  required
                />
              </div>
              
              <div className="form-group">
                <label htmlFor="bio">Bio</label>
                <textarea
                  id="bio"
                  name="bio"
                  value={formData.bio}
                  onChange={handleChange}
                  rows="4"
                ></textarea>
              </div>
              
              <div className="form-group">
                <label htmlFor="website">Website</label>
                <input
                  type="url"
                  id="website"
                  name="website"
                  value={formData.website}
                  onChange={handleChange}
                  placeholder="https://"
                />
              </div>
              
              <div className="form-group">
                <label htmlFor="discord">Discord</label>
                <input
                  type="text"
                  id="discord"
                  name="discord"
                  value={formData.discord}
                  onChange={handleChange}
                  placeholder="username#0000"
                />
              </div>
              
              <div className="form-group">
                <label htmlFor="github">GitHub</label>
                <input
                  type="text"
                  id="github"
                  name="github"
                  value={formData.github}
                  onChange={handleChange}
                  placeholder="username"
                />
              </div>
              
              <button 
                type="submit" 
                className="btn btn-primary"
                disabled={isSubmitting}
              >
                {isSubmitting ? 'Saving...' : 'Save Changes'}
              </button>
            </form>
          </div>
        )}
        
        {activeTab === 'mods' && (
          <div className="user-mods">
            <h2>My Mods</h2>
            
            <div className="mod-actions">
              <Link to="/mods/new" className="btn btn-primary">Create New Mod</Link>
            </div>
            
            {loading ? (
              <div className="loading">Loading your mods...</div>
            ) : userMods.length === 0 ? (
              <div className="no-mods">
                <p>You haven't created any mods yet.</p>
                <Link to="/mods/new" className="btn btn-primary">Create Your First Mod</Link>
              </div>
            ) : (
              <div className="mods-grid">
                {userMods.map(mod => (
                  <div key={mod.id} className="mod-card">
                    <Link to={`/mods/${mod.id}`}>
                      <div className="mod-image">
                        <img src={mod.thumbnail_url || '/default-mod-image.png'} alt={mod.name} />
                      </div>
                      <div className="mod-info">
                        <h3>{mod.name}</h3>
                        <p className="mod-description">{mod.short_description}</p>
                        <div className="mod-meta">
                          <span className="downloads">{mod.downloads} downloads</span>
                          <span className="updated">Updated: {new Date(mod.updated_at).toLocaleDateString()}</span>
                        </div>
                      </div>
                    </Link>
                    <div className="mod-actions">
                      <Link to={`/mods/${mod.id}/edit`} className="btn btn-secondary">Edit</Link>
                      <Link to={`/mods/${mod.id}/releases/new`} className="btn btn-secondary">Add Release</Link>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        )}
        
        {activeTab === 'security' && (
          <div className="security-settings">
            <h2>Security Settings</h2>
            
            <div className="password-change">
              <h3>Change Password</h3>
              <form className="password-form">
                <div className="form-group">
                  <label htmlFor="current-password">Current Password</label>
                  <input type="password" id="current-password" name="currentPassword" required />
                </div>
                
                <div className="form-group">
                  <label htmlFor="new-password">New Password</label>
                  <input type="password" id="new-password" name="newPassword" required />
                </div>
                
                <div className="form-group">
                  <label htmlFor="confirm-password">Confirm New Password</label>
                  <input type="password" id="confirm-password" name="confirmPassword" required />
                </div>
                
                <button type="submit" className="btn btn-primary">Update Password</button>
              </form>
            </div>
            
            <div className="account-deletion">
              <h3>Delete Account</h3>
              <p>Once you delete your account, there is no going back. Please be certain.</p>
              <button className="btn btn-danger">Delete My Account</button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default Profile;