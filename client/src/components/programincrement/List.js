import React, {Component} from 'react';
import {connect} from 'react-redux';
import {Link} from 'react-router-dom';
import PropTypes from 'prop-types';
import {list} from '../../actions/programincrement/list';

class List extends Component {
  static propTypes = {
    retrieved: PropTypes.object,
    loading: PropTypes.bool.isRequired,
    error: PropTypes.string,
    list: PropTypes.func.isRequired,
  };

  componentDidMount() {
    this.props.list(
      this.props.match.params.page &&
        decodeURIComponent(this.props.match.params.page)
    );
  }

  componentWillReceiveProps(nextProps) {
    if (this.props.match.params.page !== nextProps.match.params.page)
      nextProps.list(
        nextProps.match.params.page &&
          decodeURIComponent(nextProps.match.params.page)
      );
  }

  render() {
    return (
      <div>
        <h1>ProgramIncrement List</h1>

        {this.props.loading && (
          <div className="alert alert-info">Loading...</div>
        )}
        {this.props.error && (
          <div className="alert alert-danger">{this.props.error}</div>
        )}

        <table className="table table-responsive table-striped table-hover">
          <thead>
            <tr>
              <th>Name</th>
            </tr>
          </thead>
          <tbody>
            {this.props.retrieved &&
              this.props.retrieved['hydra:member'].map(item => (
                <tr key={item['@id']}>
                  <th scope="row">
                    <Link to={`show/${encodeURIComponent(item['@id'])}`}>
                      {item['name']}
                    </Link>
                  </th>
                </tr>
              ))}
          </tbody>
        </table>

        {this.pagination()}
      </div>
    );
  }

  pagination() {
    const view = this.props.retrieved && this.props.retrieved['hydra:view'];
    if (!view) return;

    const {
      'hydra:first': first,
      'hydra:previous': previous,
      'hydra:next': next,
      'hydra:last': last
    } = view;

    return (
      <nav aria-label="Page navigation">
        <Link
          to="."
          className={`btn btn-primary${previous ? '' : ' disabled'}`}
        >
          <span aria-hidden="true">&lArr;</span> First
        </Link>
        <Link
          to={
            !previous || previous === first ? '.' : encodeURIComponent(previous)
          }
          className={`btn btn-primary${previous ? '' : ' disabled'}`}
        >
          <span aria-hidden="true">&larr;</span> Previous
        </Link>
        <Link
          to={next ? encodeURIComponent(next) : '#'}
          className={`btn btn-primary${next ? '' : ' disabled'}`}
        >
          Next <span aria-hidden="true">&rarr;</span>
        </Link>
        <Link
          to={last ? encodeURIComponent(last) : '#'}
          className={`btn btn-primary${next ? '' : ' disabled'}`}
        >
          Last <span aria-hidden="true">&rArr;</span>
        </Link>
      </nav>
    );
  }
}

const mapStateToProps = state => {
  const {
    retrieved,
    loading,
    error,
  } = state.programincrement.list;
  return {retrieved, loading, error};
};

const mapDispatchToProps = dispatch => ({
  list: page => dispatch(list(page))
});

export default connect(mapStateToProps, mapDispatchToProps)(List);
