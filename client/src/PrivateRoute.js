import React from 'react';
import {connect} from 'react-redux';
import {Redirect, Route} from 'react-router-dom';

class PrivateRoute extends React.Component {
  render() {
    const {user, component: Component, ...rest} = this.props;
    const isUserAuthenticated = user && user.identity.expiresAt > Date.now();
    return (
      <Route {...rest} render={routeProps => (
        isUserAuthenticated
          ? <Component {...routeProps}/>
          : <Redirect to={{pathname: '/login', state: {from: routeProps.location}}}/>
      )}/>
    );
  }
}

const mapStateToProps = (state) => {
  return {user: state.user.account};
};

export default connect(mapStateToProps)(PrivateRoute);
