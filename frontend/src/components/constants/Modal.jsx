import '../../assets/css/components/Modal.css'

export const Modal = ({show, handleClose}) => {
    const showHideClassName = show ? 'modal display-block' : 'modal display-none';

    const handleSubmit = ()=> {
        console.log('algo');
    }

  return (
    <div className={showHideClassName}>
        <div onClick={handleClose} className="overlay">
            <form className='new_product_form' onSubmit={handleSubmit}>
                <label htmlFor='product_name'>
                    Nombre:
                    <input id='product_name' type="text" />
                </label>
                <label htmlFor='product_description'>
                    Descripción:
                    <textarea id='product_description' cols="20" rows="10"></textarea>
                </label>
                <label htmlFor="product_price">
                    Precio:
                    <input type="number" id="product_price" />
                </label>
                <label htmlFor="product_img">
                    Imagen
                    <input type="file" id="product_img" />
                </label>
                <label htmlFor="product_img-url">
                    Pegar dirección de la imagen
                    <input type="url" id="product_img-url" />
                </label>
                <button className='greenBtn' type="submit">Agregar</button>
                <button onClick={handleClose} className='redBtn' type="button">Cancelar</button>
            </form>
        </div>
    </div>
  )
}
