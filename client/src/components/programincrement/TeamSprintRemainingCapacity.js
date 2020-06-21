import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {store} from '../../store'

class TeamSprintRemainingCapacity extends React.Component {
  static propTypes = {
    projectSettings: PropTypes.object.isRequired,
    epic: PropTypes.string.isRequired,
    team: PropTypes.string.isRequired,
    sprint: PropTypes.string.isRequired,

    estimate: PropTypes.array.isRequired,
    loading: PropTypes.bool.isRequired,
    error: PropTypes.string,
  }

  getInitialCapacity() {
    if (!this.props.sprint) {
      // We must not define capacity of the Unsigned sprint (this.props.sprint equals null)
      return {frontend: 0, backend: 0};
    }

    const capacities = this.props.projectSettings.capacity;
    const capacity = capacities
        .find(c => this.props.team === c.team && this.props.sprint === c.sprint)
      || capacities.find(c => this.props.team === c.team && !c.sprint);

    return {
      frontend: (capacity && capacity.frontend) || 0,
      backend: (capacity && capacity.backend) || 0,
    }
  }

  getFilledCapacity() {
    const estimates = this.props.estimate
      .filter(e => this.props.team === e.team && this.props.sprint === e.sprint);
    const estimatesSum = type => estimates.map(e => e[type]).reduce((a, b) => a + b, 0);

    return {
      frontend: estimatesSum('frontend'),
      backend: estimatesSum('backend'),
    }
  }

  getRemainingCapacity() {
    const initialCapacity = this.getInitialCapacity();
    const filledCapacity = this.getFilledCapacity();
    return {
      frontend: initialCapacity.frontend - filledCapacity.frontend,
      backend: initialCapacity.backend - filledCapacity.backend,
    }
  }

  render() {
    let content = 'error';
    if (this.props.loading) {
      content = '...';
    } else if (this.props.error) {
      content = this.props.error;
    } else if (this.props.estimate) {
      const capacity = this.getRemainingCapacity();
      content = `${capacity.frontend} / ${capacity.backend}`;
    }
    return <span>{content}</span>;
  }
}

const mapStateToProps = (state) => {
  const {
    retrieved,
    loading,
    error,
  } = state.estimate.list;

  return {
    estimate: retrieved,
    loading,
    error,
  };
};

const ConnectedComponent = connect(mapStateToProps)(TeamSprintRemainingCapacity);
// Pass our store directly because this component is used as a child component
// inside a 3rd party component that has its own Redux store
export default (props) => <ConnectedComponent {...props} store={store}/>;
