import React, { Component } from 'react';
import { connect } from 'react-redux';
import { Link, Redirect } from 'react-router-dom';
import PropTypes from 'prop-types';
import { retrieve, reset } from '../../actions/workitem/show';
import { del } from '../../actions/workitem/delete';

class Show extends Component {
  static propTypes = {
    retrieved: PropTypes.object,
    loading: PropTypes.bool.isRequired,
    error: PropTypes.string,
    eventSource: PropTypes.instanceOf(EventSource),
    retrieve: PropTypes.func.isRequired,
    reset: PropTypes.func.isRequired,
    deleteError: PropTypes.string,
    deleteLoading: PropTypes.bool.isRequired,
    deleted: PropTypes.object,
    del: PropTypes.func.isRequired
  };

  componentDidMount() {
    this.props.retrieve(decodeURIComponent(this.props.match.params.id));
  }

  componentWillUnmount() {
    this.props.reset(this.props.eventSource);
  }

  del = () => {
    if (window.confirm('Are you sure you want to delete this item?'))
      this.props.del(this.props.retrieved);
  };

  render() {
    if (this.props.deleted) return <Redirect to=".." />;

    const item = this.props.retrieved;

    return (
      <div>
        <h1>Show {item && item['@id']}</h1>

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
        {this.props.deleteError && (
          <div className="alert alert-danger" role="alert">
            <span className="fa fa-exclamation-triangle" aria-hidden="true" />{' '}
            {this.props.deleteError}
          </div>
        )}

        {item && (
          <table className="table table-responsive table-striped table-hover">
            <thead>
              <tr>
                <th>Field</th>
                <th>Value</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">project</th>
                <td>{this.renderLinks('projects', item['project'])}</td>
              </tr>
              <tr>
                <th scope="row">team</th>
                <td>{this.renderLinks('teams', item['team'])}</td>
              </tr>
              <tr>
                <th scope="row">sprint</th>
                <td>{this.renderLinks('sprints', item['sprint'])}</td>
              </tr>
              <tr>
                <th scope="row">epic</th>
                <td>{this.renderLinks('epics', item['epic'])}</td>
              </tr>
              <tr>
                <th scope="row">backlogGroup</th>
                <td>{this.renderLinks('backlog_groups', item['backlogGroup'])}</td>
              </tr>
              <tr>
                <th scope="row">estimateFrontend</th>
                <td>{item['estimateFrontend']}</td>
              </tr>
              <tr>
                <th scope="row">estimateBackend</th>
                <td>{item['estimateBackend']}</td>
              </tr>
              <tr>
                <th scope="row">externalId</th>
                <td>{item['externalId']}</td>
              </tr>
              <tr>
                <th scope="row">name</th>
                <td>{item['name']}</td>
              </tr>
            </tbody>
          </table>
        )}
        <Link to=".." className="btn btn-primary">
          Back to list
        </Link>
        {item && (
          <Link to={`/workitems/edit/${encodeURIComponent(item['@id'])}`}>
            <button className="btn btn-warning">Edit</button>
          </Link>
        )}
        <button onClick={this.del} className="btn btn-danger">
          Delete
        </button>
      </div>
    );
  }

  renderLinks = (type, items) => {
    if (Array.isArray(items)) {
      return items.map((item, i) => (
        <div key={i}>{this.renderLinks(type, item)}</div>
      ));
    }

    return (
      <Link to={`../../${type}/show/${encodeURIComponent(items)}`}>
        {items}
      </Link>
    );
  };
}

const mapStateToProps = state => ({
  retrieved: state.workitem.show.retrieved,
  error: state.workitem.show.error,
  loading: state.workitem.show.loading,
  eventSource: state.workitem.show.eventSource,
  deleteError: state.workitem.del.error,
  deleteLoading: state.workitem.del.loading,
  deleted: state.workitem.del.deleted
});

const mapDispatchToProps = dispatch => ({
  retrieve: id => dispatch(retrieve(id)),
  del: item => dispatch(del(item)),
  reset: eventSource => dispatch(reset(eventSource))
});

export default connect(mapStateToProps, mapDispatchToProps)(Show);
