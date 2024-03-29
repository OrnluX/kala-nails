import { BrowserRouter as Router, Route, Routes} from 'react-router-dom';
import {Home} from './pages/Home';
import {Login} from './pages/Login';
import { Header } from './components/constants/Header';
import './App.css';

const App = () => {
  return (
    <Router>
      <Header/>
      <Routes>
        <Route path="/" element = {<Home/>}/>
        <Route path="/login" element={<Login/>}/>
      </Routes>
    </Router>
  )
}

export default App
