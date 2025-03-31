import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { useAuth } from '../contexts/AuthContext';

const ReleaseEditor = () => {
  const { modId, id } = useParams(); // modId for parent mod, id for release if editing
  const navigate = useNavigate();
  const { isAuthenticated } = useAuth();
  const isNewRelease = !id;

  const [formData, setFormData] = useState({
    name: '',
    version: '',
    releasedate: '',
    detailtext: '',
    statusid: '',
    modid: modId
  });
  const [mod, setMod] = useState(null);
  const [availableTags, setAvailableTags] = useState([]);
  const [selectedTags, setSelectedTags] = useState([]);
  const [statuses, setStatuses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState(null);
  const [successMessage, setSuccessMessage] = useState('');

  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/login');
      return;
    }

    const fetchData = async () => {
      try {
        setLoading(true);
        setError(null);

        // Fetch parent mod
        const modResponse = await axios.get(`/mods/${modId}`);
        setMod(modResponse.data);

        // Fetch available tags (game versions)
        const tagsResponse = await axios.get('/tags?asset_type_id=2'); // Assuming 2 is for game versions
        setAvailableTags(tagsResponse.data);

        // Fetch statuses
        const statusesResponse = await axios.get('/statuses');
        setStatuses(statusesResponse.data);

        // If editing existing release, fetch its data
        if (!isNewRelease) {
          const releaseResponse = await axios.get(`/releases/${id}`);
          const release = releaseResponse.data;
          
          setFormData({
            name: release.name,
            version: release.version,
            releasedate: release.releasedate,
            detailtext: release.detailtext,
            statusid: release.statusid,
            modid: release.modid
          });
          
          // Fetch release tags
          const releaseTagsResponse = await axios.get(`/releases/${id}/tags`);
          setSelectedTags(releaseTagsResponse.data);
        } else {
          // Set default status to draft for new releases
          const draftStatus = statusesResponse.data.find(status => status.code === 'draft');
          if (draftStatus) {
            setFormData(prev => ({ ...prev, statusid: draftStatus.id }));
          }
        }

        setLoading(false);
      } catch (err) {
        console.error('Error fetching data:', err);
        setError('Failed to load data. Please try again later.');
        setLoading(false);
      }
    };

    fetchData();
  }, [id, modId, isAuthenticated, navigate, isNewRelease]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleTagToggle = (tagId) => {
    if (selectedTags.some(tag => tag.id === tagId)) {
      setSelectedTags(selectedTags.filter(tag => tag.id !== tagId));
    } else {
      const tagToAdd = availableTags.find(tag => tag.id === tagId);
      setSelectedTags([...selectedTags, tagToAdd]);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      setSaving(true);
      setError(null);
      setSuccessMessage('');

      const payload = {
        ...formData,
        tag_ids: selectedTags.map(tag => tag.id)
      };

      let response;
      if (isNewRelease) {
        response = await axios.post('/releases', payload);
        setSuccessMessage('Release created successfully!');
        // Redirect to the new release page after a short delay
        setTimeout(() => navigate(`/releases/${response.data.id}`), 1500);
      } else {
        response = await axios.put(`/releases/${id}`, payload);
        setSuccessMessage('Release updated successfully!');
      }

      setSaving(false);
    } catch (err) {
      console.error('Error saving release:', err);
      setError('Failed to save release. Please check your inputs and try again.');
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="release-editor__loading">
        <p>Loading editor...</p>
      </div>
    );
  }

  return (
    <div className="release-editor">
      <h1>{isNewRelease ? 'Create New Release' : 'Edit Release'}</h1>
      <h2>For Mod: {mod?.name}</h2>
      
      {error && (
        <div className="release-editor__error">
          <p>{error}</p>
        </div>
      )}
      
      {successMessage && (
        <div className="release-editor__success">
          <p>{successMessage}</p>
        </div>
      )}
      
      <form onSubmit={handleSubmit} className="release-editor__form">
        <div className="form-group">
          <label htmlFor="name">Release Name</label>
          <input
            type="text"
            id="name"
            name="name"
            value={formData.name}
            onChange={handleChange}
            required
            className="form-control"
          />
        </div>
        
        <div className="form-group">
          <label htmlFor="version">Version</label>
          <input
            type="text"
            id="version"
            name="version"
            value={formData.version}
            onChange={handleChange}
            required
            className="form-control"
            placeholder="e.g. 1.0.0"
          />
        </div>
        
        <div className="form-group">
          <label htmlFor="releasedate">Release Date</label>
          <input
            type="date"
            id="releasedate"
            name="releasedate"
            value={formData.releasedate}
            onChange={handleChange}
            className="form-control"
          />
        </div>
        
        <div className="form-group">
          <label htmlFor="detailtext">Release Notes</label>
          <textarea
            id="detailtext"
            name="detailtext"
            value={formData.detailtext}
            onChange={handleChange}
            className="form-control"
            rows="10"
            placeholder="Describe what's new in this release"
          />
        </div>
        
        <div className="form-group">
          <label htmlFor="statusid">Status</label>
          <select
            id="statusid"
            name="statusid"
            value={formData.statusid}
            onChange={handleChange}
            className="form-control"
            required
          >
            <option value="">Select Status</option>
            {statuses.map(status => (
              <option key={status.id} value={status.id}>
                {status.name}
              </option>
            ))}
          </select>
          {formData.statusid && statuses.find(s => s.id === formData.statusid)?.code === 'draft' && (
            <small className="form-text text-muted">
              Draft releases are only visible to you and won't appear in public listings.
            </small>
          )}
        </div>
        
        <div className="form-group">
          <label>Game Versions</label>
          <div className="tag-selector">
            {availableTags.map(tag => (
              <div key={tag.id} className="tag-option">
                <input
                  type="checkbox"
                  id={`tag-${tag.id}`}
                  checked={selectedTags.some(t => t.id === tag.id)}
                  onChange={() => handleTagToggle(tag.id)}
                />
                <label htmlFor={`tag-${tag.id}`} style={{ backgroundColor: tag.color }}>
                  {tag.name}
                </label>
              </div>
            ))}
          </div>
        </div>
        
        <div className="form-group">
          <label>Files</label>
          <p className="file-upload-info">
            You can upload files after saving the release.
          </p>
        </div>
        
        <div className="form-actions">
          <button 
            type="submit" 
            className="btn btn-primary" 
            disabled={saving}
          >
            {saving ? 'Saving...' : (isNewRelease ? 'Create Release' : 'Update Release')}
          </button>
          <button 
            type="button" 
            className="btn btn-secondary" 
            onClick={() => navigate(`/mods/${modId}`)}
            disabled={saving}
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  );
};

export default ReleaseEditor;