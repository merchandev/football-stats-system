import { useState, useEffect } from 'react';
import { championshipsAPI, teamsAPI, playersAPI, matchesAPI } from '../services/api';

function Dashboard() {
    const [stats, setStats] = useState({
        totalChampionships: 0,
        totalTeams: 0,
        totalPlayers: 0,
        totalMatches: 0
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadDashboardStats();
    }, []);

    const loadDashboardStats = async () => {
        try {
            const [championships, teams, players, matches] = await Promise.all([
                championshipsAPI.getAll(),
                teamsAPI.getAll(),
                playersAPI.getAll(),
                matchesAPI.getAll()
            ]);

            setStats({
                totalChampionships: championships.data.length,
                totalTeams: teams.data.length,
                totalPlayers: players.data.length,
                totalMatches: matches.data.length
            });
        } catch (error) {
            console.error('Error loading dashboard stats:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className="loading">
                <div className="spinner"></div>
                <p>Cargando estadísticas...</p>
            </div>
        );
    }

    return (
        <div className="page-container">
            <div className="page-header">
                <h1 className="page-title">Dashboard</h1>
                <p className="page-subtitle">Resumen general del sistema de estadísticas</p>
            </div>

            <div className="stat-grid">
                <div className="stat-card" style={{ background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }}>
                    <div className="stat-label">Total Campeonatos</div>
                    <div className="stat-value">{stats.totalChampionships}</div>
                </div>

                <div className="stat-card" style={{ background: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' }}>
                    <div className="stat-label">Total Equipos</div>
                    <div className="stat-value">{stats.totalTeams}</div>
                </div>

                <div className="stat-card" style={{ background: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)' }}>
                    <div className="stat-label">Total Jugadoras</div>
                    <div className="stat-value">{stats.totalPlayers}</div>
                </div>

                <div className="stat-card" style={{ background: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)' }}>
                    <div className="stat-label">Total Partidos</div>
                    <div className="stat-value">{stats.totalMatches}</div>
                </div>
            </div>

            <div className="card">
                <h2 className="card-title">Bienvenido al Sistema de Estadísticas de Fútbol Femenino</h2>
                <p style={{ marginBottom: '1rem', color: 'var(--gray)' }}>
                    Este sistema te permite gestionar y analizar datos completos sobre campeonatos, equipos, jugadoras y partidos.
                </p>

                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '1.5rem', marginTop: '2rem' }}>
                    <div>
                        <h3 style={{ marginBottom: '0.5rem' }}>📊 Gestión de Datos</h3>
                        <ul style={{ color: 'var(--gray)', lineHeight: '1.8' }}>
                            <li>Registrar campeonatos y formatos</li>
                            <li>Administrar equipos y jugadoras</li>
                            <li>Registrar partidos completos con detalles</li>
                            <li>Gestionar directores técnicos y jueces</li>
                        </ul>
                    </div>

                    <div>
                        <h3 style={{ marginBottom: '0.5rem' }}>📈 Estadísticas Avanzadas</h3>
                        <ul style={{ color: 'var(--gray)', lineHeight: '1.8' }}>
                            <li>Tablas de posiciones</li>
                            <li>Top goleadoras y asistidoras</li>
                            <li>Rendimiento por equipo y jugadora</li>
                            <li>Historial de enfrentamientos</li>
                        </ul>
                    </div>

                    <div>
                        <h3 style={{ marginBottom: '0.5rem' }}>📄 Exportación</h3>
                        <ul style={{ color: 'var(--gray)', lineHeight: '1.8' }}>
                            <li>Reportes en PDF</li>
                            <li>Exportación a Excel</li>
                            <li>Estadísticas personalizadas</li>
                            <li>Análisis detallados</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Dashboard;
