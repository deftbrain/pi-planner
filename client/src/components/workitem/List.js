import React, {Component} from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {list, reset} from '../../actions/workitem/list';

class List extends Component {
  static propTypes = {
    epic: PropTypes.string.isRequired,
    retrieved: PropTypes.object,
    loading: PropTypes.bool.isRequired,
    error: PropTypes.string,
    eventSource: PropTypes.instanceOf(EventSource),
    deletedItem: PropTypes.object,
    list: PropTypes.func.isRequired,
    reset: PropTypes.func.isRequired
  };

  componentDidMount() {
    this.props.list(this.props.epic);
  }

  componentWillUnmount() {
    this.props.reset(this.props.eventSource);
  }

  render() {
    return (
      <div>
        {this.props.loading && (
          <div className="alert alert-info">Loading...</div>
        )}
        {this.props.deletedItem && (
          <div className="alert alert-success">
            {this.props.deletedItem['@id']} deleted.
          </div>
        )}
        {this.props.error && (
          <div className="alert alert-danger">{this.props.error}</div>
        )}

        <ul>
          {this.props.retrieved && this.props.retrieved['hydra:member'].map(item => (
            <li key={item['@id']}>{item['name']}</li>
          ))}
        </ul>
      </div>
    );
  }
}

const mapStateToProps = (state, ownProps) => {
  const {
    retrieved,
    loading,
    error,
    eventSource,
    deletedItem
  } = state.workitem.list;
  return {retrieved: retrieved[ownProps.epic], loading, error, eventSource, deletedItem};
};

const mapDispatchToProps = dispatch => ({
  list: epic => dispatch(list(epic)),
  reset: eventSource => dispatch(reset(eventSource))
});

export default connect(mapStateToProps, mapDispatchToProps)(List);
