import { LoaderPage } from "./LoaderPage";
import { Table } from "./Table";
import { AddProduct } from "./AddProduct";
import { Modal } from "./constants/Modal";
import { useState ,useEffect } from "react";
import axios from 'axios';
import '../assets/css/components/Dashboard.css'

const siteUrl = "http://localhost/kalanailsmenu";

export const Dashboard = () => {
  const [loading, setLoading] = useState(true);
  const [products, setProducts] = useState([]);
  const [showModal, setShowModal] = useState(false);
  
  const handleOpenModal = () => {
    setShowModal(true);
  };

  const handleCloseModal = () => {
    setShowModal(false);
  };

  useEffect(()=>{
    const getData = async () => {
      try {
        const response = await axios.get(
          `${siteUrl}/backendPHP/product/product.php`
        );  
        setProducts(response.data);
      } 
      catch (error) {
        console.log(error);
      } finally {
        setLoading(false);
      }
    }
    getData();
  },[])

  // const handleLogout = () => {
  //   localStorage.removeItem('token');
  //   window.location.reload(true);
  // }

  return (
    <>  
        <h1>Dashboard</h1>
        {
          loading ? (
            <LoaderPage/>
          ) : (
            <>
              <Table data={products}/>
              <AddProduct showModal={handleOpenModal}/>
              <Modal show={showModal} handleClose={handleCloseModal} />
            </>
          )
        }
        {/* <button onClick={handleLogout}>Cerrar sesión</button> */}
    </>
  )
}

