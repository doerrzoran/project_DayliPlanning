import { useState } from 'react';
import '../styles/Nav.css';
import Logout from '../Utils/Logout';
import { useNavigate } from 'react-router';

export default function Nav() {
    const [isOpen, setIsOpen] = useState(false);
    const navigate = useNavigate();

    const handleLogout = () => {
        localStorage.removeItem('authToken')
        navigate('/')     
    }
  
    return (
        <>
        {/* Bouton mobile */}
        <button 
            className="mobile-menu-btn"
            onClick={() => setIsOpen(!isOpen)}
        >
            {/* Icône menu */}
        </button>

        {/* Overlay mobile */}
        <div 
            className={`mobile-overlay ${isOpen ? 'active' : ''}`}
            onClick={() => setIsOpen(false)}
        />

        {/* Sidebar */}
        <nav className={`sidebar-nav ${isOpen ? 'open' : 'closed'}`}>
            <button className="nav-toggle-btn" onClick={() => setIsOpen(!isOpen)}>
            {/* Icône chevron */}
            </button>

            <div className="nav-container">
            <ul className="nav-menu">
                <li className="nav-item">
                <a href="/tag" className="nav-link active">
                    <span className="nav-icon">🕐</span>
                    <span className="nav-label">Badgeage</span>
                </a>
                </li>
                <li className="nav-item">
                <a href="/calendar" className="nav-link active">
                    <span className="nav-icon">📅</span>
                    <span className="nav-label">planning</span>
                </a>
                </li>
                
                <li className="nav-item">
                <a href="#" className="nav-link active">
                    <span className="nav-icon">📝</span>
                    <span className="nav-label">demande d'absence</span>
                </a>
                </li>
                
                {/* Plus d'items... */}
            </ul>

                <button onClick={handleLogout}>
                    <span className="nav-icon">📝</span>
                    <span className="nav-label">deconnexion</span>
                </button>
            
            </div>
        </nav>
        </>
    );
}