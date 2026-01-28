function PlayerStats() {
    return (
        <div className="page-container">
            <div className="page-header">
                <h1 className="page-title">Estadísticas por Jugadora</h1>
                <p className="page-subtitle">Análisis individual de rendimiento</p>
            </div>
            <div className="card">
                <p style={{ color: 'var(--gray)' }}>
                    Página de estadísticas por jugadora - permite ver partidos jugados, goles,
                    asistencias, tarjetas, minutos jugados y rivales contra los que ha marcado.
                </p>
            </div>
        </div>
    );
}

export default PlayerStats;
