import React, { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import axios from 'axios';

const ReleaseDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [release, setRelease] = useState(null);
  const [mod, setMod] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [downloading, setDownloading] = useState(false);

  useEffect(() => {
    const fetchReleaseData = async () => {
      try {
        setLoading(true);
        // Fetch release details
        const releaseResponse = await axios.get(`/releases/${id}`);
        setRelease(releaseResponse.data);
        
        // Fetch parent mod details
        const modResponse = await axios.get(`/mods/${releaseResponse.data.mod_id}`);
        setMod(modResponse.data);
        
        setLoading(false);
      } catch (err) {
        setError('Failed to load release details. Please try again later.');
        setLoading(false);
        console.error('Error fetching release details:', err);
      }
    };

    fetchReleaseData();
  }, [id]);

  const handleDownload = async () => {
    try {
      setDownloading(true);
      const response = await axios.get(`/releases/${id}/download`, {
        responseType: 'blob'
      });
      
      // Create a download link
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `${release.file_name}`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      
      // Increment download count
      await axios.post(`/releases/${id}/download-count`);
      
      setDownloading(false);
    } catch (err) {
      setError('Failed to download the file. Please try again later.');
      setDownloading(false);
      console.error('Error downloading file:', err);
    }
  };

  if (loading) {
    return (
      <div className="release-detail__loading">
        <p>Loading release details...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="release-detail__error">
        <p>{error}</p>
        <button onClick={() => navigate(-1)} className="btn btn-primary">Go Back</button>
      </div>
    );
  }

  if (!release || !mod) {
    return (
      <div className="release-detail__not-found">
        <h2>Release Not Found</h2>
        <p>The release you're looking for doesn't exist or has been removed.</p>
        <Link to="/mods" className="btn btn-primary">Browse Mods</Link>
      </div>
    );
  }

  return (
    <div className="release-detail">
      <div className="release-detail__header">
        <div className="release-detail__breadcrumb">
          <Link to="/mods">Mods</Link> &gt; 
          <Link to={`/mods/${mod.id}`}>{mod.name}</Link> &gt; 
          <span>Version {release.version}</span>
        </div>
        
        <h1 className="release-detail__title">
          {mod.name} - Version {release.version}
        </h1>
        
        <div className="release-detail__meta">
          <span className="release-detail__date">
            Released on {new Date(release.created_at).toLocaleDateString()}
          </span>
          <span className="release-detail__downloads">
            {release.downloads} Downloads
          </span>
        </div>
      </div>

      <div className="release-detail__content">
        <div className="release-detail__main">
          <div className="release-detail__description">
            <h2>Release Notes</h2>
            <div dangerouslySetInnerHTML={{ __html: release.description }} />
          </div>
          
          <div className="release-detail__compatibility">
            <h3>Compatibility</h3>
            <ul className="release-detail__compatibility-list">
              <li><strong>Game Version:</strong> {release.game_version}</li>
              <li><strong>API Version:</strong> {release.api_version}</li>
              {release.dependencies && release.dependencies.length > 0 && (
                <li>
                  <strong>Dependencies:</strong>
                  <ul className="release-detail__dependencies">
                    {release.dependencies.map((dep, index) => (
                      <li key={index}>
                        <Link to={`/mods/${dep.mod_id}`}>{dep.name}</Link> (v{dep.version})
                      </li>
                    ))}
                  </ul>
                </li>
              )}
            </ul>
          </div>
        </div>

        <div className="release-detail__sidebar">
          <div className="release-detail__download-card">
            <h3>Download</h3>
            <div className="release-detail__file-info">
              <p><strong>File Name:</strong> {release.file_name}</p>
              <p><strong>File Size:</strong> {(release.file_size / 1024 / 1024).toFixed(2)} MB</p>
            </div>
            
            <button 
              onClick={handleDownload} 
              className="btn btn-primary btn-block"
              disabled={downloading}
            >
              {downloading ? 'Downloading...' : 'Download Now'}
            </button>
          </div>
          
          <div className="release-detail__mod-card">
            <h3>About {mod.name}</h3>
            <p className="release-detail__mod-description">
              {mod.short_description || mod.description.substring(0, 150) + '...'}
            </p>
            <Link to={`/mods/${mod.id}`} className="btn btn-outline btn-block">
              View Mod Page
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ReleaseDetail;