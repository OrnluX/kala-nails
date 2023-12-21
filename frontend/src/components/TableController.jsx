import { Table } from './Table';
import { TextBox } from './TextBox';
import { AddProduct } from "./AddProduct";
import { Modal } from "./constants/Modal";
import { useEffect, useState } from 'react';

import '../assets/css/components/Table.css';

export const TableController = ({data}) => {
    const [showTextBox, setShowTextBox] = useState(false);
    const [deleteElement, setElementDelete] = useState(null);
    const [showModal, setShowModal] = useState(false);

    /**
     * Función que modifica el estado de showModal a "true". Esto muestra la ventana modal.
     * @return VOID
     */
    const handleOpenModal = () => {
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
            <Modal show={showModal} handleClose={handleCloseModal} />
            {
                showTextBox
                ? (<TextBox elementToDelete={deleteElement} cancellDelete={handleCancellBtn} />)
                : (null)
            }
        </>
  )
}

