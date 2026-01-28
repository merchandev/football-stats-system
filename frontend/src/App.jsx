import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import { useState } from 'react';
import './App.css';

// Pages (we'll create these)
import Dashboard from './pages/Dashboard';
import Championships from './pages/Championships';
import Teams from './pages/Teams';
import Players from './pages/Players';
import Matches from './pages/Matches';
import ChampionshipStats from './pages/ChampionshipStats';
import TeamStats from './pages/TeamStats';
import PlayerStats from './pages/PlayerStats';

function App() {
  const [sidebarOpen, setSidebarOpen] = useState(true);

  return (
    <Router>
      <div className="app">
        {/* Sidebar */}
        <aside className={`sidebar ${sidebarOpen ? 'open' : 'closed'}`}>
          <div className="sidebar-header">
            <h1>⚽ FutStats</h1>
            <p>Sistema de Estadísticas de Fútbol Femenino</p>
          </div>
          <nav className="sidebar-nav">
            <Link to="/" className="nav-item">
              <span className="icon">📊</span>
              Dashboard
            </Link>

            <div className="nav-section">Gestión de Datos</div>
            <Link to="/championships" className="nav-item">
              <span className="icon">🏆</span>
              Campeonatos
            </Link>
            <Link to="/teams" className="nav-item">
              <span className="icon">🛡️</span>
              Equipos
            </Link>
            <Link to="/players" className="nav-item">
              <span className="icon">👤</span>
              Jugadoras
            </Link>
            <Link to="/matches" className="nav-item">
              <span className="icon">⚽</span>
              Partidos
            </Link>

            <div className="nav-section">Estadísticas</div>
            <Link to="/stats/championships" className="nav-item">
              <span className="icon">📈</span>
              Por Campeonato
            </Link>
            <Link to="/stats/teams" className="nav-item">
              <span className="icon">📉</span>
              Por Equipo
            </Link>
            <Link to="/stats/players" className="nav-item">
              <span className="icon">🌟</span>
              Por Jugadora
            </Link>
          </nav>
        </aside>

        {/* Main Content */}
        <main className="main-content">
          <button
            className="sidebar-toggle"
            onClick={() => setSidebarOpen(!sidebarOpen)}
          >
            {sidebarOpen ? '◀' : '▶'}
          </button>

          <Routes>
            <Route path="/" element={<Dashboard />} />
            <Route path="/championships" element={<Championships />} />
            <Route path="/teams" element={<Teams />} />
            <Route path="/players" element={<Players />} />
            <Route path="/matches" element={<Matches />} />
            <Route path="/stats/championships" element={<ChampionshipStats />} />
            <Route path="/stats/teams" element={<TeamStats />} />
            <Route path="/stats/players" element={<PlayerStats />} />
          </Routes>
        </main>
      </div>
    </Router>
  );
}

export default App;
