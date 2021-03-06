import React, {Component} from 'react';
import {connect} from 'react-redux';
import {Link} from 'react-router-dom';
import PropTypes from 'prop-types';
import {retrieve} from '../../actions/programincrement/show';
import EpicsList from '../epic/List'
import Header from './Header';

class Show extends Component {
  static propTypes = {
    retrieved: PropTypes.object,
    loading: PropTypes.bool.isRequired,
    error: PropTypes.string,
    retrieve: PropTypes.func.isRequired,
  };

  componentDidMount() {
    this.props.retrieve(decodeURIComponent(this.props.match.params.id));
  }

  render() {
    const item = this.props.retrieved;

    return (
      <div>
        <Header title={item && `Program Increment: ${item['name']}`}/>

        {this.props.loading && (
          <div className="alert alert-info" role="status">
            Loading...
          </div>
        )}
        {this.props.error && (
          <div className="alert alert-danger" role="alert">
            <span className="fa fa-exclamation-triangle" aria-hidden="true" />{' '}
            {this.props.error}
          </div>
        )}

        {item && <EpicsList programIncrement={item}/>}
      </div>
    );
  }
}

const mapStateToProps = state => ({
  retrieved: state.programincrement.show.retrieved,
  error: state.programincrement.show.error,
  loading: state.programincrement.show.loading,
});

const mapDispatchToProps = dispatch => ({
  retrieve: id => dispatch(retrieve(id)),
});

export default connect(mapStateToProps, mapDispatchToProps)(Show);
