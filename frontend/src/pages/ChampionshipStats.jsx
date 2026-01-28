import { useState, useEffect } from 'react';
import { championshipsAPI } from '../services/api';
import { exportAPI } from '../services/api';

function ChampionshipStats() {
    const [championships, setChampionships] = useState([]);
    const [selectedChampionship, setSelectedChampionship] = useState(null);
    const [standings, setStandings] = useState([]);
    const [topScorers, setTopScorers] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadChampionships();
    }, []);

    const loadChampionships = async () => {
        try {
            const response = await championshipsAPI.getAll();
            setChampionships(response.data);
            if (response.data.length > 0) {
                setSelectedChampionship(response.data[0].id);
                loadStats(response.data[0].id);
            }
        } catch (error) {
            console.error('Error loading championships:', error);
        } finally {
            setLoading(false);
        }
    };

    const loadStats = async (championshipId) => {
        try {
            const [stdgs, scorers] = await Promise.all([
                championshipsAPI.getStandings(championshipId),
                championshipsAPI.getTopScorers(championshipId)
            ]);
            setStandings(stdgs.data);
            setTopScorers(scorers.data);
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    };

    const handleChampionshipChange = (e) => {
        const championshipId = e.target.value;
        setSelectedChampionship(championshipId);
        loadStats(championshipId);
    };

    if (loading) {
        return <div className="loading"><div className="spinner"></div></div>;
    }

    return (
        <div className="page-container">
            <div className="page-header">
                <h1 className="page-title">Estadísticas por Campeonato</h1>
                <p className="page-subtitle">Análisis detallado de competiciones</p>
            </div>

            <div className="card" style={{ marginBottom: '2rem' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem' }}>
                    <div style={{ flex: 1 }}>
                        <label className="form-label">Seleccionar Campeonato</label>
                        <select
                            className="form-input"
                            value={selectedChampionship || ''}
                            onChange={handleChampionshipChange}
                            style={{ maxWidth: '400px' }}
                        >
                            {championships.map((championship) => (
                                <option key={championship.id} value={championship.id}>
                                    {championship.name} ({championship.year})
                                </option>
                            ))}
                        </select>
                    </div>
                    {selectedChampionship && (
                        <div style={{ display: 'flex', gap: '1rem' }}>
                            <button
                                className="btn btn-danger"
                                onClick={() => exportAPI.exportPDF('championship', selectedChampionship)}
                            >
                                📄 Exportar PDF
                            </button>
                            <button
                                className="btn btn-success"
                                onClick={() => exportAPI.exportExcel('championship', selectedChampionship)}
                            >
                                📊 Exportar Excel
                            </button>
                        </div>
                    )}
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.5rem' }}>
                <div className="card">
                    <h2 className="card-title">Tabla de Posiciones</h2>
                    <div className="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Pos</th>
                                    <th>Equipo</th>
                                    <th>PJ</th>
                                    <th>Pts</th>
                                    <th>DG</th>
                                </tr>
                            </thead>
                            <tbody>
                                {standings.map((standing, index) => (
                                    <tr key={standing.id}>
                                        <td><strong>{index + 1}</strong></td>
                                        <td>{standing.name}</td>
                                        <td>{(standing.wins || 0) + (standing.draws || 0) + (standing.losses || 0)}</td>
                                        <td><strong>{standing.points || 0}</strong></td>
                                        <td>{standing.goal_difference || 0}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="card">
                    <h2 className="card-title">Top Goleadoras</h2>
                    <div className="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Pos</th>
                                    <th>Jugadora</th>
                                    <th>Equipo</th>
                                    <th>Goles</th>
                                </tr>
                            </thead>
                            <tbody>
                                {topScorers.map((scorer, index) => (
                                    <tr key={scorer.id}>
                                        <td><strong>{index + 1}</strong></td>
                                        <td>{scorer.first_name} {scorer.last_name}</td>
                                        <td>{scorer.team_name}</td>
                                        <td><strong style={{ color: 'var(--success)' }}>{scorer.total_goals}</strong></td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default ChampionshipStats;
