import { Table } from '../components/Table';
import { TextBox } from '../components/TextBox';
import { AddProduct } from "../components/AddProduct";
import { Modal } from "../components/constants/Modal";
import { useEffect, useState } from 'react';

import '../assets/css/components/Table.css';

export const TableController = ({dataAsChanged, data}) => {
    const [showTextBox, setShowTextBox] = useState(false);
    const [deleteElement, setElementDelete] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [modalContent, setModalContent] = useState({
        productName: '',
        productDescription: '',
        productPrice: 0,
        productImgURL: '',
        productImgFile: null,
    });

    /**
     * Función que modifica el estado de showModal a "true". Esto muestra la ventana modal.
     * @return VOID
     */
    const handleOpenModal = (content) => {
        setModalContent((prevContent) => ({
            ...prevContent,
            productName: content.name,
            productDescription: content.description,
            productPrice: content.price,
            productImgURL: content.imgURL,
        }))
        setShowModal(true);
    };
    
    /**
     * Función que modifica el estado de showModal a "false". Esto oculta la ventana modal.
     * @return VOID
     */
    const handleCloseModal = () => {
        setShowModal(false);
    };

    /**Función que se pasa como prop al componente TextBox para manejar el click en el botón de cancelar (referente a eliminar un producto o no). Setea el estado de visibilidad de dicho componente hijo.
     * @return VOID
     */
    const handleCancellBtn = () => {
        setShowTextBox(false);
    }

    /**Función que se pasa como prop al componente TableBtn para manejar el evento "click" del botón de eliminar producto. Recibe la información del producto que se desea eliminar y almacena dicha información en un estado "deleteElement"
     * @param OBJECT elementInfo
     * @return VOID
    */
    const handleElementInfo = (elementInfo) => { 
        setElementDelete(elementInfo);
    }
    useEffect(() => { //Este efecto depende del cambio de la información del estado deleteElement. Cuando el componente hijo TableBtn envía la información y se guarda en dicho estado, "showTextBox" cambia a true y se muestra la ventana modal.
        if (deleteElement === null) {
            setShowTextBox(false);
        } else {
            setShowTextBox(true);
        }
    },[deleteElement])

    return (                                
        <>  
            <Table products={data} handleElement={handleElementInfo}/>
            <AddProduct showModal={handleOpenModal}/>
            {
                showModal
                ? (<Modal 
                    content={modalContent} //Contenido de la ventana modal
                    show={showModal} 
                    handleClose={handleCloseModal} 
                    dataChanged={dataAsChanged}
                />)
                :(null)
            }
            {
                showTextBox
                ? (<TextBox 
                        elementToDelete={deleteElement} 
                        closeTextBox={handleCancellBtn}
                        dataChanged={dataAsChanged}
                />)
                : (null)
            }
        </>
  )
}

