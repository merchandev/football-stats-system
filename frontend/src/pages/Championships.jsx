import { useState, useEffect } from 'react';
import { championshipsAPI } from '../services/api';

function Championships() {
    const [championships, setChampionships] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showForm, setShowForm] = useState(false);
    const [formData, setFormData] = useState({
        name: '',
        format: '',
        year: new Date().getFullYear(),
        start_date: '',
        end_date: '',
        country: '',
        description: ''
    });

    useEffect(() => {
        loadChampionships();
    }, []);

    const loadChampionships = async () => {
        try {
            const response = await championshipsAPI.getAll();
            setChampionships(response.data);
        } catch (error) {
            console.error('Error loading championships:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            await championshipsAPI.create(formData);
            setShowForm(false);
            setFormData({
                name: '',
                format: '',
                year: new Date().getFullYear(),
                start_date: '',
                end_date: '',
                country: '',
                description: ''
            });
            loadChampionships();
        } catch (error) {
            console.error('Error creating championship:', error);
            alert('Error al crear el campeonato');
        }
    };

    const handleDelete = async (id) => {
        if (window.confirm('¿Estás seguro de eliminar este campeonato?')) {
            try {
                await championshipsAPI.delete(id);
                loadChampionships();
            } catch (error) {
                console.error('Error deleting championship:', error);
                alert('Error al eliminar el campeonato');
            }
        }
    };

    if (loading) {
        return (
            <div className="loading">
                <div className="spinner"></div>
                <p>Cargando campeonatos...</p>
            </div>
        );
    }

    return (
        <div className="page-container">
            <div className="page-header">
                <h1 className="page-title">Campeonatos</h1>
                <p className="page-subtitle">Gestión de torneos y competiciones</p>
            </div>

            <div style={{ marginBottom: '1.5rem' }}>
                <button className="btn btn-primary" onClick={() => setShowForm(!showForm)}>
                    {showForm ? '❌ Cancelar' : '➕ Nuevo Campeonato'}
                </button>
            </div>

            {showForm && (
                <div className="card" style={{ marginBottom: '2rem' }}>
                    <h2 className="card-title">Crear Nuevo Campeonato</h2>
                    <form onSubmit={handleSubmit}>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
                            <div className="form-group">
                                <label className="form-label">Nombre del Campeonato *</label>
                                <input
                                    type="text"
                                    className="form-input"
                                    value={formData.name}
                                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                    required
                                />
                            </div>

                            <div className="form-group">
                                <label className="form-label">Formato *</label>
                                <select
                                    className="form-input"
                                    value={formData.format}
                                    onChange={(e) => setFormData({ ...formData, format: e.target.value })}
                                    required
                                >
                                    <option value="">Seleccionar...</option>
                                    <option value="League">Liga</option>
                                    <option value="Cup">Copa</option>
                                    <option value="Tournament">Torneo</option>
                                </select>
                            </div>

                            <div className="form-group">
                                <label className="form-label">Año *</label>
                                <input
                                    type="number"
                                    className="form-input"
                                    value={formData.year}
                                    onChange={(e) => setFormData({ ...formData, year: e.target.value })}
                                    required
                                />
                            </div>

                            <div className="form-group">
                                <label className="form-label">País</label>
                                <input
                                    type="text"
                                    className="form-input"
                                    value={formData.country}
                                    onChange={(e) => setFormData({ ...formData, country: e.target.value })}
                                />
                            </div>

                            <div className="form-group">
                                <label className="form-label">Fecha Inicio</label>
                                <input
                                    type="date"
                                    className="form-input"
                                    value={formData.start_date}
                                    onChange={(e) => setFormData({ ...formData, start_date: e.target.value })}
                                />
                            </div>

                            <div className="form-group">
                                <label className="form-label">Fecha Fin</label>
                                <input
                                    type="date"
                                    className="form-input"
                                    value={formData.end_date}
                                    onChange={(e) => setFormData({ ...formData, end_date: e.target.value })}
                                />
                            </div>
                        </div>

                        <div className="form-group">
                            <label className="form-label">Descripción</label>
                            <textarea
                                className="form-input"
                                value={formData.description}
                                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                                rows="3"
                            />
                        </div>

                        <button type="submit" className="btn btn-success">✔️ Crear Campeonato</button>
                    </form>
                </div>
            )}

            <div className="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Formato</th>
                            <th>Año</th>
                            <th>País</th>
                            <th>Período</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {championships.map((championship) => (
                            <tr key={championship.id}>
                                <td><strong>{championship.name}</strong></td>
                                <td>{championship.format}</td>
                                <td>{championship.year}</td>
                                <td>{championship.country || '-'}</td>
                                <td>
                                    {championship.start_date && championship.end_date
                                        ? `${championship.start_date} - ${championship.end_date}`
                                        : '-'}
                                </td>
                                <td>
                                    <div className="actions">
                                        <button className="btn btn-primary" style={{ padding: '0.5rem 1rem' }}>
                                            📊 Ver Stats
                                        </button>
                                        <button
                                            className="btn btn-danger"
                                            style={{ padding: '0.5rem 1rem' }}
                                            onClick={() => handleDelete(championship.id)}
                                        >
                                            🗑️ Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

export default Championships;
