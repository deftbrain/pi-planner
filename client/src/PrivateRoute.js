import React from 'react';
import {Redirect, Route} from 'react-router-dom';
import {isUserAuthenticated} from './authProvider';

export default props => {
  const {component: Component, ...rest} = props;

  return (
    <Route {...rest} render={routeProps => (
      isUserAuthenticated()
        ? <Component {...routeProps}/>
        : <Redirect to={{pathname: '/login', state: {from: routeProps.location}}}/>
    )}/>
  );
}
