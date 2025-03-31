import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import axios from 'axios';

const ModList = () => {
  const [mods, setMods] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [tags, setTags] = useState([]);
  const [selectedTags, setSelectedTags] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [sortBy, setSortBy] = useState('created_at');
  const [sortOrder, setSortOrder] = useState('desc');
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const location = useLocation();

  // Parse query parameters
  useEffect(() => {
    const queryParams = new URLSearchParams(location.search);
    const tagParam = queryParams.get('tags');
    const searchParam = queryParams.get('search');
    const sortParam = queryParams.get('sort');
    const orderParam = queryParams.get('order');
    const pageParam = queryParams.get('page');

    if (tagParam) setSelectedTags(tagParam.split(','));
    if (searchParam) setSearchQuery(searchParam);
    if (sortParam) setSortBy(sortParam);
    if (orderParam) setSortOrder(orderParam);
    if (pageParam) setCurrentPage(parseInt(pageParam, 10));
  }, [location]);

  // Fetch tags
  useEffect(() => {
    const fetchTags = async () => {
      try {
        const response = await axios.get('/tags');
        setTags(response.data);
      } catch (err) {
        console.error('Error fetching tags:', err);
      }
    };

    fetchTags();
  }, []);

  // Fetch mods based on filters
  useEffect(() => {
    const fetchMods = async () => {
      setLoading(true);
      setError(null);

      try {
        const params = new URLSearchParams();
        params.append('page', currentPage);
        params.append('sort', sortBy);
        params.append('order', sortOrder);
        
        if (searchQuery) params.append('search', searchQuery);
        if (selectedTags.length > 0) params.append('tags', selectedTags.join(','));

        const response = await axios.get(`/mods?${params.toString()}`);
        setMods(response.data.mods);
        setTotalPages(response.data.total_pages || 1);
        setLoading(false);
      } catch (err) {
        console.error('Error fetching mods:', err);
        setError('Failed to load mods. Please try again later.');
        setLoading(false);
      }
    };

    fetchMods();
  }, [currentPage, sortBy, sortOrder, searchQuery, selectedTags]);

  const handleTagToggle = (tagId) => {
    setSelectedTags(prev => {
      if (prev.includes(tagId)) {
        return prev.filter(id => id !== tagId);
      } else {
        return [...prev, tagId];
      }
    });
    setCurrentPage(1); // Reset to first page when changing filters
  };

  const handleSearch = (e) => {
    e.preventDefault();
    setCurrentPage(1); // Reset to first page when searching
  };

  const handleSortChange = (e) => {
    const [sort, order] = e.target.value.split('-');
    setSortBy(sort);
    setSortOrder(order);
    setCurrentPage(1); // Reset to first page when changing sort
  };

  const handlePageChange = (page) => {
    if (page < 1 || page > totalPages) return;
    setCurrentPage(page);
    window.scrollTo(0, 0);
  };

  return (
    <div className="mod-list-container">
      <h1>Browse Mods</h1>
      
      <div className="mod-list-controls">
        <div className="search-container">
          <form onSubmit={handleSearch}>
            <input
              type="text"
              placeholder="Search mods..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="search-input"
            />
            <button type="submit" className="btn btn-primary">Search</button>
          </form>
        </div>
        
        <div className="sort-container">
          <select 
            value={`${sortBy}-${sortOrder}`} 
            onChange={handleSortChange}
            className="sort-select"
          >
            <option value="created_at-desc">Newest First</option>
            <option value="created_at-asc">Oldest First</option>
            <option value="downloads-desc">Most Downloads</option>
            <option value="name-asc">Name (A-Z)</option>
            <option value="name-desc">Name (Z-A)</option>
            <option value="updated_at-desc">Recently Updated</option>
          </select>
        </div>
      </div>
      
      <div className="mod-list-content">
        <div className="tag-filter">
          <h3>Filter by Tags</h3>
          <div className="tag-list">
            {tags.map(tag => (
              <div 
                key={tag.id} 
                className={`tag ${selectedTags.includes(tag.id) ? 'selected' : ''}`}
                onClick={() => handleTagToggle(tag.id)}
              >
                {tag.name}
              </div>
            ))}
          </div>
        </div>
        
        <div className="mods-grid">
          {loading ? (
            <div className="loading">Loading mods...</div>
          ) : error ? (
            <div className="error">{error}</div>
          ) : mods.length === 0 ? (
            <div className="no-results">No mods found matching your criteria.</div>
          ) : (
            mods.map(mod => (
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
                      <span className="updated">Updated: {new Date(mod.updated_at).toLocaleDateString()}</span>
                    </div>
                    <div className="mod-tags">
                      {mod.tags.slice(0, 3).map(tag => (
                        <span key={tag.id} className="tag">{tag.name}</span>
                      ))}
                      {mod.tags.length > 3 && <span className="more-tags">+{mod.tags.length - 3} more</span>}
                    </div>
                  </div>
                </Link>
              </div>
            ))
          )}
        </div>
      </div>
      
      {totalPages > 1 && (
        <div className="pagination">
          <button 
            onClick={() => handlePageChange(currentPage - 1)}
            disabled={currentPage === 1}
            className="btn btn-secondary"
          >
            Previous
          </button>
          
          <div className="page-numbers">
            {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
              let pageNum;
              if (totalPages <= 5) {
                pageNum = i + 1;
              } else if (currentPage <= 3) {
                pageNum = i + 1;
              } else if (currentPage >= totalPages - 2) {
                pageNum = totalPages - 4 + i;
              } else {
                pageNum = currentPage - 2 + i;
              }
              
              return (
                <button
                  key={pageNum}
                  onClick={() => handlePageChange(pageNum)}
                  className={`btn ${currentPage === pageNum ? 'btn-primary' : 'btn-secondary'}`}
                >
                  {pageNum}
                </button>
              );
            })}
          </div>
          
          <button 
            onClick={() => handlePageChange(currentPage + 1)}
            disabled={currentPage === totalPages}
            className="btn btn-secondary"
          >
            Next
          </button>
        </div>
      )}
    </div>
  );
}