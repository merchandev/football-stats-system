import { useState, useEffect } from 'react';
import { teamsAPI } from '../services/api';

function Teams() {
    const [teams, setTeams] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadTeams();
    }, []);

    const loadTeams = async () => {
        try {
            const response = await teamsAPI.getAll();
            setTeams(response.data);
        } catch (error) {
            console.error('Error loading teams:', error);
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
                <h1 className="page-title">Equipos</h1>
                <p className="page-subtitle">Gestión de clubes y equipos</p>
            </div>
            <div className="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Ciudad</th>
                            <th>País</th>
                            <th>Estadio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {teams.map((team) => (
                            <tr key={team.id}>
                                <td><strong>{team.name}</strong></td>
                                <td>{team.city || '-'}</td>
                                <td>{team.country || '-'}</td>
                                <td>{team.stadium_home || '-'}</td>
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

export default Teams;
