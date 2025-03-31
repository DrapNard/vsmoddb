import React from 'react';

const TagSelector = ({ availableTags, selectedTags, onTagToggle }) => {
  return (
    <div className="tag-selector">
      {availableTags.map(tag => (
        <div key={tag.id} className="tag-option">
          <input
            type="checkbox"
            id={`tag-${tag.id}`}
            checked={selectedTags.some(t => t.id === tag.id)}
            onChange={() => onTagToggle(tag.id)}
          />
          <label 
            htmlFor={`tag-${tag.id}`} 
            className="tag-label"
            style={{ backgroundColor: tag.color || '#91A357' }}
          >
            {tag.name}
          </label>
        </div>
      ))}
      
      {availableTags.length === 0 && (
        <p className="tag-selector__empty">No tags available</p>
      )}
    </div>
  );
};

export default TagSelector;