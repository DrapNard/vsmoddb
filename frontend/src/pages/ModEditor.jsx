import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { useAuth } from '../contexts/AuthContext';

const ModEditor = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { isAuthenticated, currentUser } = useAuth();
  const isNewMod = !id;

  const [formData, setFormData] = useState({
    name: '',
    code: '',
    text: '',
    statusid: '',
    tags: []
  });
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

        // Fetch available tags
        const tagsResponse = await axios.get('/tags?asset_type_id=1'); // Assuming 1 is for mods
        setAvailableTags(tagsResponse.data);

        // Fetch statuses
        const statusesResponse = await axios.get('/statuses');
        setStatuses(statusesResponse.data);

        // If editing existing mod, fetch its data
        if (!isNewMod) {
          const modResponse = await axios.get(`/mods/${id}`);
          const mod = modResponse.data;
          
          setFormData({
            name: mod.name,
            code: mod.code,
            text: mod.text,
            statusid: mod.statusid
          });
          
          // Fetch mod tags
          const modTagsResponse = await axios.get(`/mods/${id}/tags`);
          setSelectedTags(modTagsResponse.data);
        } else {
          // Set default status to draft for new mods
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
  }, [id, isAuthenticated, navigate, isNewMod]);

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
      if (isNewMod) {
        response = await axios.post('/mods', payload);
        setSuccessMessage('Mod created successfully!');
        // Redirect to the new mod page after a short delay
        setTimeout(() => navigate(`/mods/${response.data.id}`), 1500);
      } else {
        response = await axios.put(`/mods/${id}`, payload);
        setSuccessMessage('Mod updated successfully!');
      }

      setSaving(false);
    } catch (err) {
      console.error('Error saving mod:', err);
      setError('Failed to save mod. Please check your inputs and try again.');
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="mod-editor__loading">
        <p>Loading editor...</p>
      </div>
    );
  }

  return (
    <div className="mod-editor">
      <h1>{isNewMod ? 'Create New Mod' : 'Edit Mod'}</h1>
      
      {error && (
        <div className="mod-editor__error">
          <p>{error}</p>
        </div>
      )}
      
      {successMessage && (
        <div className="mod-editor__success">
          <p>{successMessage}</p>
        </div>
      )}
      
      <form onSubmit={handleSubmit} className="mod-editor__form">
        <div className="form-group">
          <label htmlFor="name">Mod Name</label>
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
          <label htmlFor="code">Mod Code</label>
          <input
            type="text"
            id="code"
            name="code"
            value={formData.code}
            onChange={handleChange}
            required
            className="form-control"
            placeholder="Unique identifier for your mod"
          />
        </div>
        
        <div className="form-group">
          <label htmlFor="text">Description</label>
          <textarea
            id="text"
            name="text"
            value={formData.text}
            onChange={handleChange}
            className="form-control"
            rows="10"
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
              Draft mods are only visible to you and won't appear in public listings.
            </small>
          )}
        </div>
        
        <div className="form-group">
          <label>Tags</label>
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
        
        <div className="form-actions">
          <button 
            type="submit" 
            className="btn btn-primary" 
            disabled={saving}
          >
            {saving ? 'Saving...' : (isNewMod ? 'Create Mod' : 'Update Mod')}
          </button>
          <button 
            type="button" 
            className="btn btn-secondary" 
            onClick={() => navigate(isNewMod ? '/mods' : `/mods/${id}`)}
            disabled={saving}
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  );
};

export default ModEditor;