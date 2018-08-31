'use strict';

import React from "react"
import PropTypes from 'prop-types'
import {translateMessage} from '../Component/MessageTranslator'
import Messages from '../Component/Messages/Messages'
import ButtonDelete from '../Component/Button/ButtonDelete'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faLink, faPlusCircle } from '@fortawesome/free-solid-svg-icons'

export default function AddressCollection(props) {
    const {
        translations,
        suggestions,
        addAddress,
        addressSuggestions,
        messages,
        cancelMessage,
        attached,
        parentClass,
        removeAddress,
        inputSearch,
        newAddress
    } = props

    let search = ''
    
    const options = suggestions.map(address =>
        <div className='row row-striped' key={address.id} value={address.id} onClick={addAddress}>
            <div className='col-10 offset-1 possibleAddress' value={address.id} >
                {address.label}
            </div>
        </div>)

    const attachedAddresses = attached.map(address => {
        let button = {
            label: 'address.remove',
            style: {float: 'right'},
            classMerge: 'btn-sm',
            response_type: 'json',
        }
        let url = '/address/{id}/remove/{parentClass}/{entity_id}/'
        url = url.replace('{id}', address.id)
        url = url.replace('{parentClass}', parentClass)

        return (
            <div className='row row-striped' key={address.id}>
                <div className='col-8'>{address.label}</div>
                <div className='col-4'>{parentClass === address.parent ? (
                    <ButtonDelete
                        button={button}
                        url={url}
                        buttonClickAction={removeAddress}
                        translations={translations}
                    />) : <div className='btn-light btn-sm text-right'><FontAwesomeIcon icon={faLink} /></div> }</div>
            </div>
        )
    })

    return (
        <div>
            <Messages
                messages={messages}
                translations={translations}
                cancelMessage={cancelMessage}
            />
            <div className="input-group input-group-sm">
                <input
                    placeholder={translateMessage(translations, 'address.search.placeholder')}
                    ref={input => search = input}
                    onChange={addressSuggestions}
                    value={inputSearch}
                    className='form-control'
                />
                <span className='input-group-append'>
                    <button className='btn btn-success' type={'button'} onClick={() => newAddress()} title={translateMessage(translations, 'address.button.add')}><FontAwesomeIcon icon={faPlusCircle}/></button>
                </span>
            </div>
            <div className='small' style={{maxHeight: '200px', overflowY: 'scroll', cursor: 'pointer'}}>
                {options}
            </div>

            {attachedAddresses}
        </div>
    )
}

AddressCollection.propTypes = {
    translations: PropTypes.object.isRequired,
    suggestions: PropTypes.array.isRequired,
    addressSuggestions: PropTypes.func.isRequired,
    addAddress: PropTypes.func.isRequired,
    removeAddress: PropTypes.func.isRequired,
    newAddress: PropTypes.func.isRequired,
    cancelMessage: PropTypes.func.isRequired,
    parentClass: PropTypes.string.isRequired,
    inputSearch: PropTypes.string.isRequired,
}