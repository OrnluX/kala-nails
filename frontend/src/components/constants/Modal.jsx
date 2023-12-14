import '../../assets/css/components/Modal.css'

export const Modal = ({show, handleClose}) => {
    const showHideClassName = show ? 'modal display-block' : 'modal display-none';

    const handleSubmit = ()=> {
        console.log('algo');
    }

  return (
    <div className={`${showHideClassName} overlay`}>
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

        </form>
    </div>
  )
}
