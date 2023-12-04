import { useState, useEffect } from "react";
import LoginForm from "../components/LoginForm";
import Dashboard from "../components/Dashboard";

const Login = () => {
  const [authData, setAuthData] = useState({
    isLogged: false,
    isTokenStored: false,
  });

  const checkTokenValidity = (newToken) => {
    const isValidToken = newToken !== "" && newToken.length > 30;
    setAuthData({
      ...authData,
      isLogged: isValidToken,
    });
  };

  useEffect(() => {
    const fetchToken = localStorage.getItem("token");
    const isNullToken = fetchToken === null;
    setAuthData({
      ...authData,
      isTokenStored: !isNullToken,
      isLogged: !isNullToken,
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [authData.isTokenStored]);

  const { isLogged } = authData;

  return (
    <>
      {!isLogged ? <LoginForm sendToken={checkTokenValidity} /> : <Dashboard />}
    </>
  );
};

export default Login;
