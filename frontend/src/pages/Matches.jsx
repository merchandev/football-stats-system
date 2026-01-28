import { useState, useEffect } from 'react';
import { matchesAPI } from '../services/api';

function Matches() {
    const [matches, setMatches] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadMatches();
    }, []);

    const loadMatches = async () => {
        try {
            const response = await matchesAPI.getAll();
            setMatches(response.data);
        } catch (error) {
            console.error('Error loading matches:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return <div className="loading"><div className="spinner"></div></div>;
    }

    return (
        <div className="page-container">
            <div className="page-header">
                <h1 className="page-title">Partidos</h1>
                <p className="page-subtitle">Gestión de partidos y resultados</p>
            </div>
            <div className="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Campeonato</th>
                            <th>Local</th>
                            <th>Result</th>
                            <th>Visitante</th>
                            <th>Estadio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {matches.map((match) => (
                            <tr key={match.id}>
                                <td>{match.match_date}</td>
                                <td>{match.championship_name}</td>
                                <td><strong>{match.home_team_name}</strong></td>
                                <td style={{ textAlign: 'center', fontWeight: 'bold', color: 'var(--primary)' }}>
                                    {match.home_score} - {match.away_score}
                                </td>
                                <td><strong>{match.away_team_name}</strong></td>
                                <td>{match.stadium_name || '-'}</td>
                                <td>
                                    <button className="btn btn-primary" style={{ padding: '0.5rem 1rem' }}>
                                        📝 Detalles
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

export default Matches;
