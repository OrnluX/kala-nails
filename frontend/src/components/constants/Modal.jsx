/* eslint-disable react-hooks/exhaustive-deps */
import { useEffect, useState } from 'react';
import '../../assets/css/components/Modal.css';
import axios from 'axios';

export const Modal = ({ handleClose, dataChanged, content}) => {

    const [initialContent, setInitialContent] = useState({
        
    })

    const [productData, setProductData] = useState({
        productName: '',
        description: '',
        price: 0,
        productImgURL: '',
        productImg: null
    });

    const [responseStatus, setResponseStatus] = useState(0);
    
    const siteURL = "http://localhost/kalanailsmenu";

    const handleSubmit = async (e)=> { //Funcion que maneja el evento submit del formulario
        e.preventDefault();
        try {
            const formData = new FormData();
            formData.append('nombre', productData.productName);
            formData.append('descripcion', productData.description);
            formData.append('precio', productData.price);
            formData.append('imagen', productData.productImgURL);
            formData.append('imgFile', productData.productImg);
            
            const response = await axios.post(`${siteURL}/backendPHP/product/product.php`, formData);
            
            console.log(response.data.message);
            setResponseStatus(response.status);
            
            if (responseStatus === 200) {
                dataChanged();
                handleClose();
            } 
        
        } catch (error) {
            console.log(error.response.data.message);
            setResponseStatus(error.response.status);
        }
    }
    useEffect(() => {
        // Lógica basada en responseStatus
        if (responseStatus === 200) {
            dataChanged();
            handleClose();
        } 
    }, [responseStatus]);

  return (
    <div>
        <div className="overlay">
            <form 
                className='new_product_form' 
                encType="multipart/form-data" 
                onSubmit={handleSubmit}>
                
                <label htmlFor='product_name'>
                    Nombre:
                    <input 
                        id='product_name' 
                        name='nombre'
                        type="text" 
                        onChange={(e) => setProductData((prevData) =>({
                            ...prevData,
                            productName: e.target.value,
                        }))} 
                    />
                </label>
                
                <label htmlFor='product_description'>
                    Descripción:
                    <textarea 
                        id='product_description' 
                        name='descripcion'
                        cols="20" 
                        rows="10"
                        onChange={(e) => setProductData((prevData) => ({
                            ...prevData,
                            description: e.target.value,
                        }))}
                        >      
                    </textarea>
                </label>
                
                <label htmlFor="product_price">
                    Precio:
                    <input 
                        type="number" 
                        id="product_price"
                        name='precio'
                        onChange={(e) => setProductData((prevData) => ({
                            ...prevData,
                            price: e.target.value,
                        }))} 
                    />
                </label>
                
                <label htmlFor="product_img">
                    Imagen
                    <input 
                        type="file" 
                        id="product_img" 
                        name='product_img'
                        accept='image/*' 
                        onChange={(e) => setProductData((prevData) => ({
                            ...prevData,
                            productImg: e.target.files[0],
                        }))}
                    />
                </label>
                
                <label htmlFor="product_img-url">
                    Pegar dirección de la imagen
                    <input 
                        type="url" 
                        id="product_img-url"
                        name='imagen'
                        onChange={(e) => setProductData((prevData) => ({
                            ...prevData,
                            productImgURL: e.target.value,
                        }))}
                    />
                </label>
                
                <button className='greenBtn' type="submit">Agregar</button>
                
                <button onClick={handleClose} className='redBtn' type="button">Cancelar</button>
            </form>
        </div>
    </div>
  )
}
