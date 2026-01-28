import { useState, useEffect } from 'react';
import { playersAPI } from '../services/api';

function Player() {
    const [players, setPlayers] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadPlayers();
    }, []);

    const loadPlayers = async () => {
        try {
            const response = await playersAPI.getAll();
            setPlayers(response.data);
        } catch (error) {
            console.error('Error loading players:', error);
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
                <h1 className="page-title">Jugadoras</h1>
                <p className="page-subtitle">Gestión de jugadoras</p>
            </div>
            <div className="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Posición</th>
                            <th>Nacionalidad</th>
                            <th>Equipo Actual</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {players.map((player) => (
                            <tr key={player.id}>
                                <td><strong>{player.first_name} {player.last_name}</strong></td>
                                <td>{player.position || '-'}</td>
                                <td>{player.nationality || '-'}</td>
                                <td>{player.team_name || '-'}</td>
                                <td>
                                    <button className="btn btn-primary" style={{ padding: '0.5rem 1rem' }}>
                                        📊 Ver Stats
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

export default Player;
