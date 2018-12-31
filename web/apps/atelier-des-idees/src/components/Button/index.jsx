import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

function Button(props) {
    return (
        <button
            className={classNames('button', `button--${props.mode}`, props.className)}
            aria-label={props.label}
            disabled={props.disabled}
            onClick={() => props.onClick()}
        >
            {props.icon && (
                <img src={props.icon} className={classNames('button__icon', `button__icon--${props.classIcon}`)} />
            )}
            <span className="button__label">{props.label}</span>
        </button>
    );
}

Button.defaultProps = {
    label: undefined,
    mode: 'primary',
    icon: undefined,
    type: 'button',
    disabled: false,
    className: undefined,
    classIcon: undefined,
    isLoading: false,
};

Button.propTypes = {
    label: PropTypes.string,
    mode: PropTypes.oneOf(['primary', 'secondary', 'tertiary']),
    icon: PropTypes.string,
    type: PropTypes.string,
    onClick: PropTypes.func.isRequired,
    disabled: PropTypes.bool,
    className: PropTypes.string,
    classIcon: PropTypes.oneOf(['start', 'end']),
    isLoading: PropTypes.bool,
};

export default Button;
