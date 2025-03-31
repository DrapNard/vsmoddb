import React, { lazy, Suspense } from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import ThemeToggle from './components/ThemeToggle';

// Import main SCSS file
import './styles/main.scss';

// Components
import Header from './components/Header';
import Footer from './components/Footer';

// Lazy load pages for better code splitting
const Home = lazy(() => import('./pages/Home'));
const Login = lazy(() => import('./pages/Login'));
const Register = lazy(() => import('./pages/Register'));
const Profile = lazy(() => import('./pages/Profile'));
const ModList = lazy(() => import('./pages/ModList'));
const ModDetail = lazy(() => import('./pages/ModDetail'));
const ModEditor = lazy(() => import('./pages/ModEditor'));
const ReleaseDetail = lazy(() => import('./pages/ReleaseDetail'));
const ReleaseEditor = lazy(() => import('./pages/ReleaseEditor'));
const UserProfile = lazy(() => import('./pages/UserProfile'));
const Notifications = lazy(() => import('./pages/Notifications'));

// Loading component for suspense fallback
const LoadingPage = () => (
  <div className="loading-container">
    <div className="loading-spinner"></div>
    <p>Loading...</p>
  </div>
);

// Protected route component
const ProtectedRoute = ({ children }) => {
  const { isAuthenticated } = useAuth();
  
  if (!isAuthenticated) {
    return <Navigate to="/login" />;
  }
  
  return children;
};

function App() {
  return (
    <AuthProvider>
      <Router>
        <div className="app-container">
          <Header />
          <main className="main-content">
            <Suspense fallback={<LoadingPage />}>
              <Routes>
                <Route path="/" element={<Home />} />
                <Route path="/login" element={<Login />} />
                <Route path="/register" element={<Register />} />
                <Route path="/mods" element={<ModList />} />
                <Route path="/mods/:id" element={<ModDetail />} />
                <Route path="/mods/new" element={<ModEditor />} />
                <Route path="/mods/:id/edit" element={<ModEditor />} />
                <Route path="/releases/:id" element={<ReleaseDetail />} />
                <Route path="/mods/:modId/releases/new" element={<ReleaseEditor />} />
                <Route path="/releases/:id/edit" element={<ReleaseEditor />} />
                <Route path="/users/:id" element={<UserProfile />} />
                <Route path="/notifications" element={<Notifications />} />
                <Route 
                  path="/profile" 
                  element={
                    <ProtectedRoute>
                      <Profile />
                    </ProtectedRoute>
                  } 
                />
              </Routes>
            </Suspense>
          </main>
          <Footer />
          <ThemeToggle />
        </div>
      </Router>
    </AuthProvider>
  );
}

export default App;