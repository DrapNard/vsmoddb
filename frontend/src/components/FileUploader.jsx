import React, { useState } from 'react';
import axios from 'axios';
import { useAuth } from '../contexts/AuthContext';

const FileUploader = ({ assetId, assetType, onUploadComplete }) => {
  const [selectedFile, setSelectedFile] = useState(null);
  const [uploading, setUploading] = useState(false);
  const [progress, setProgress] = useState(0);
  const [error, setError] = useState(null);
  const { currentUser } = useAuth();

  const handleFileChange = (e) => {
    setSelectedFile(e.target.files[0]);
    setError(null);
  };

  const handleUpload = async (e) => {
    e.preventDefault();
    
    if (!selectedFile) {
      setError('Please select a file to upload');
      return;
    }

    try {
      setUploading(true);
      setProgress(0);
      setError(null);

      // Create form data
      const formData = new FormData();
      formData.append('file', selectedFile);
      formData.append('assetid', assetId);
      formData.append('assettypeid', assetType === 'mod' ? 1 : 2); // Assuming 1 for mod, 2 for release

      // Upload file with progress tracking
      const response = await axios.post('/files', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        onUploadProgress: (progressEvent) => {
          const percentCompleted = Math.round(
            (progressEvent.loaded * 100) / progressEvent.total
          );
          setProgress(percentCompleted);
        }
      });

      setUploading(false);
      setSelectedFile(null);
      
      // Call the callback function with the uploaded file data
      if (onUploadComplete) {
        onUploadComplete(response.data);
      }
    } catch (err) {
      console.error('Error uploading file:', err);
      setError('Failed to upload file. Please try again.');
      setUploading(false);
    }
  };

  return (
    <div className="file-uploader">
      <form onSubmit={handleUpload} className="file-uploader__form">
        <div className="file-uploader__input-group">
          <input
            type="file"
            onChange={handleFileChange}
            className="file-uploader__input"
            disabled={uploading}
          />
          <button
            type="submit"
            className="file-uploader__button"
            disabled={!selectedFile || uploading}
          >
            {uploading ? 'Uploading...' : 'Upload'}
          </button>
        </div>
        
        {uploading && (
          <div className="file-uploader__progress">
            <div 
              className="file-uploader__progress-bar" 
              style={{ width: `${progress}%` }}
            ></div>
            <span className="file-uploader__progress-text">{progress}%</span>
          </div>
        )}
        
        {error && (
          <div className="file-uploader__error">
            {error}
          </div>
        )}
      </form>
    </div>
  );
};

export default FileUploader;