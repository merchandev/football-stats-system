function TeamStats() {
    return (
        <div className="page-container">
            <div className="page-header">
                <h1 className="page-title">Estadísticas por Equipo</h1>
                <p className="page-subtitle">Análisis de clubes y rendimiento</p>
            </div>
            <div className="card">
                <p style={{ color: 'var(--gray)' }}>
                    Página de estadísticas por equipo - permite ver títulos ganados, partidos jugados,
                    goleadoras históricas, y análisis head-to-head contra rivales.
                </p>
            </div>
        </div>
    );
}

export default TeamStats;
