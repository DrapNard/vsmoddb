import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useAuth } from '../contexts/AuthContext';

const CommentSection = ({ assetId }) => {
  const [comments, setComments] = useState([]);
  const [newComment, setNewComment] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [sortOrder, setSortOrder] = useState('desc'); // 'asc' for oldest first, 'desc' for newest first
  const [editingComment, setEditingComment] = useState(null);
  const [editText, setEditText] = useState('');
  const { isAuthenticated, currentUser } = useAuth();

  useEffect(() => {
    fetchComments();
  }, [assetId, sortOrder]);

  const fetchComments = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`/comments?asset_id=${assetId}&sort=created&order=${sortOrder}`);
      setComments(response.data);
      setLoading(false);
    } catch (err) {
      console.error('Error fetching comments:', err);
      setError('Failed to load comments. Please try again later.');
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!newComment.trim()) return;

    try {
      const response = await axios.post('/comments', {
        assetid: assetId,
        text: newComment
      });

      // Add the new comment to the list
      if (sortOrder === 'desc') {
        setComments([response.data, ...comments]);
      } else {
        setComments([...comments, response.data]);
      }

      setNewComment('');
    } catch (err) {
      console.error('Error posting comment:', err);
      setError('Failed to post comment. Please try again.');
    }
  };

  const handleEdit = (comment) => {
    setEditingComment(comment.id);
    setEditText(comment.text);
  };

  const handleCancelEdit = () => {
    setEditingComment(null);
    setEditText('');
  };

  const handleSaveEdit = async (commentId) => {
    try {
      const response = await axios.put(`/comments/${commentId}`, {
        text: editText
      });

      // Update the comment in the list
      setComments(comments.map(comment => 
        comment.id === commentId ? { ...comment, text: editText } : comment
      ));

      setEditingComment(null);
      setEditText('');
    } catch (err) {
      console.error('Error updating comment:', err);
      setError('Failed to update comment. Please try again.');
    }
  };

  const handleDelete = async (commentId) => {
    if (!window.confirm('Are you sure you want to delete this comment?')) return;

    try {
      await axios.delete(`/comments/${commentId}`);

      // Remove the comment from the list
      setComments(comments.filter(comment => comment.id !== commentId));
    } catch (err) {
      console.error('Error deleting comment:', err);
      setError('Failed to delete comment. Please try again.');
    }
  };

  const toggleSortOrder = () => {
    setSortOrder(sortOrder === 'asc' ? 'desc' : 'asc');
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleString();
  };

  const canModify = (comment) => {
    if (!currentUser) return false;
    return currentUser.id === comment.userid || currentUser.admin || currentUser.moderator;
  };

  if (loading && comments.length === 0) {
    return <div className="comments__loading">Loading comments...</div>;
  }

  return (
    <div className="comments-section">
      <h3 id="comments">
        {comments.length} Comments
        <span className="comments-sort">
          (<a href="#" onClick={(e) => { e.preventDefault(); setSortOrder('asc'); }}>
            oldest first
          </a> | <a href="#" onClick={(e) => { e.preventDefault(); setSortOrder('desc'); }}>
            newest first
          </a>)
        </span>
      </h3>

      {error && <div className="comments__error">{error}</div>}

      {isAuthenticated && (
        <div className="comment comment-editor">
          <div className="comment__title">
            {currentUser.name}, add a comment:
          </div>
          <form onSubmit={handleSubmit}>
            <textarea
              className="comment__textarea"
              value={newComment}
              onChange={(e) => setNewComment(e.target.value)}
              placeholder="Write your comment here..."
              required
            />
            <button type="submit" className="btn btn-primary">
              Add Comment
            </button>
          </form>
        </div>
      )}

      <div className="comments-list">
        {comments.length === 0 ? (
          <p>No comments yet. Be the first to comment!</p>
        ) : (
          comments.map(comment => (
            <div key={comment.id} id={`cmt-${comment.id}`} className="comment">
              <div className="comment__title">
                <a href={`#cmt-${comment.id}`} className="comment__anchor">💬</a>
                {comment.username}
                {comment.isbanned && <span className="comment__banned"> [currently restricted]</span>}
                , {formatDate(comment.created)}
                {comment.modifieddate && <span> (modified at {formatDate(comment.modifieddate)})</span>}
                {comment.lastmodaction && <span> (edited by moderator)</span>}
                
                {canModify(comment) && (
                  <span className="comment__actions">
                    (<a href="#" onClick={(e) => { e.preventDefault(); handleEdit(comment); }}>edit comment</a>
                    <a href="#" onClick={(e) => { e.preventDefault(); handleDelete(comment.id); }}> delete</a>)
                  </span>
                )}
              </div>
              
              {editingComment === comment.id ? (
                <div className="comment__edit">
                  <textarea
                    className="comment__textarea"
                    value={editText}
                    onChange={(e) => setEditText(e.target.value)}
                  />
                  <div className="comment__edit-actions">
                    <button 
                      className="btn btn-primary" 
                      onClick={() => handleSaveEdit(comment.id)}
                    >
                      Save
                    </button>
                    <button 
                      className="btn btn-secondary" 
                      onClick={handleCancelEdit}
                    >
                      Cancel
                    </button>
                  </div>
                </div>
              ) : (
                <div className="comment__body">{comment.text}</div>
              )}
            </div>
          ))
        )}
      </div>
    </div>
  );
};

export default CommentSection;