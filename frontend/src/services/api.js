import axios from 'axios';

// Create axios instances for V1 and V2 API
const apiV1 = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json'
  }
});

const apiV2 = axios.create({
  baseURL: '/api/v2',
  headers: {
    'Content-Type': 'application/json'
  }
});

// Apply interceptors to both instances
const applyInterceptors = (api) => {
  api.interceptors.request.use(
    (config) => {
      const token = localStorage.getItem('token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    },
    (error) => Promise.reject(error)
  );

  api.interceptors.response.use(
    (response) => response,
    async (error) => {
      if (error.response?.status === 401) {
        localStorage.removeItem('token');
        window.location.href = '/login';
      }
      return Promise.reject(error);
    }
  );
};

applyInterceptors(apiV1);
applyInterceptors(apiV2);



// Auth endpoints
export const auth = {
  login: (credentials) => apiV1.post('/auth/login', credentials),
  register: (userData) => apiV1.post('/auth/register', userData),
  logout: () => apiV1.post('/auth/logout'),
  me: () => apiV1.get('/auth/me')
};

// Mods endpoints
export const mods = {
  getAll: (params) => apiV1.get('/mods', { params }),
  getOne: (id) => apiV1.get(`/mods/${id}`),
  create: (data) => apiV1.post('/mods', data),
  update: (id, data) => apiV1.put(`/mods/${id}`, data),
  delete: (id) => apiV1.delete(`/mods/${id}`),
  getTags: (id) => apiV1.get(`/mods/${id}/tags`),
  getReleases: (id) => apiV1.get(`/mods/${id}/releases`)
};

// Releases endpoints
export const releases = {
  getAll: (params) => apiV1.get('/releases', { params }),
  getOne: (id) => apiV1.get(`/releases/${id}`),
  create: (data) => apiV1.post('/releases', data),
  update: (id, data) => apiV1.put(`/releases/${id}`, data),
  delete: (id) => apiV1.delete(`/releases/${id}`),
  download: (id) => apiV1.get(`/releases/${id}/download`),
  updateDownloadCount: (id) => apiV1.post(`/releases/${id}/download-count`)
};

// Users endpoints
export const users = {
  getProfile: (id) => apiV1.get(`/users/${id}`),
  updateProfile: (id, data) => apiV1.put(`/users/${id}`, data),
  getMods: (id) => apiV1.get(`/users/${id}/mods`)
};

// Comments endpoints
export const comments = {
  getAll: (params) => apiV1.get('/comments', { params }),
  create: (data) => apiV1.post('/comments', data),
  update: (id, data) => apiV1.put(`/comments/${id}`, data),
  delete: (id) => apiV1.delete(`/comments/${id}`)
};

// Files endpoints
export const files = {
  upload: (formData) => apiV1.post('/files', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
};

// Tags endpoints
export const tags = {
  getAll: (params) => apiV1.get('/tags', { params })
};

// Notifications endpoints (V2)
export const notifications = {
  getAll: () => apiV2.get('/notifications'),
  getOne: (id) => apiV2.get(`/notifications/${id}`),
  getAllNotifications: () => apiV2.get('/notifications/all'),
  clearAll: () => apiV2.post('/notifications/clear')
};

export default {
  auth,
  mods,
  releases,
  users,
  comments,
  files,
  tags,
  notifications
};