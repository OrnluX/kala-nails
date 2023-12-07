export const Dashboard = () => {
  
  const handleLogout = () => {
    localStorage.removeItem('token');
    window.location.reload(true);
  }

  return (
    <>
        <h1>Dashboard</h1>
        <button onClick={handleLogout}>Cerrar sesión</button>
    </>
  )
}

