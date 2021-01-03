import React, {Fragment} from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {makeStyles} from '@material-ui/core/styles';

function getInitialCapacity(team, sprint, capacity) {
  const teamSprintCapacity = capacity.find(c => team === c.team && sprint === c.sprint)
    || capacity.find(c => team === c.team && !c.sprint);

  return {
    frontend: (teamSprintCapacity && teamSprintCapacity.capacity.frontend) || 0,
    backend: (teamSprintCapacity && teamSprintCapacity.capacity.backend) || 0,
  }
}

function getFilledCapacity(team, sprint, estimate) {
  const teamSprintEstimate = estimate.filter(e => team === e.team && sprint === e.sprint);
  const sumEstimates = type => teamSprintEstimate.map(e => e[type]).reduce((a, b) => a + b, 0);

  return {
    frontend: sumEstimates('frontend'),
    backend: sumEstimates('backend'),
  }
}

function getRemainingTeamSprintCapacity(team, sprint, capacity, estimate) {
  const initialCapacity = getInitialCapacity(team, sprint, capacity);
  const filledCapacity = getFilledCapacity(team, sprint, estimate);
  return {
    frontend: initialCapacity.frontend - filledCapacity.frontend,
    backend: initialCapacity.backend - filledCapacity.backend,
  }
}

function getRemainingCapacity(teams, sprints, capacity, estimate) {
  const result = {};

  for (let team of teams) {
    let teamRemainingCapacity = {total: {frontend: 0, backend: 0}};
    for (let sprint of sprints) {
      const remainingTeamSprintCapacity = getRemainingTeamSprintCapacity(team['@id'], sprint['@id'], capacity, estimate);
      teamRemainingCapacity[sprint.id] = remainingTeamSprintCapacity;
      teamRemainingCapacity.total.frontend += remainingTeamSprintCapacity.frontend;
      teamRemainingCapacity.total.backend += remainingTeamSprintCapacity.backend;
    }
    result[team.id] = teamRemainingCapacity;
  }

  return result;
}

const useStyles = makeStyles(theme => ({
  root: {
    padding: theme.spacing(2),
    position: 'sticky',
    top: 0,
    zIndex: theme.zIndex.speedDial,
    background: theme.palette.background.default,
  },
  table: {
    width: '100%',
    borderCollapse: 'collapse',
    '& th,td': {
      border: '1px solid black',
      textAlign: 'center',
    },
  },
  headerCell: {
    width: props => `${100 / (props.sprints ? props.sprints['hydra:member'].length + 1 : 6)}%`,
  }
}));

const TeamSprintCapacity = props => {
  const classes = useStyles(props);
  if (!props.teams || !props.sprints || !props.programIncrement || !props.estimate) {
    return null;
  }

  const remainingCapacity = getRemainingCapacity(
    props.teams['hydra:member'],
    props.sprints['hydra:member'],
    props.programIncrement.teamSprintCapacities,
    props.estimate
  );

  return (
    <div className={classes.root}>
      <table className={classes.table}>
        <thead>
        <tr>
          <th className={classes.headerCell} colSpan={3}>Team</th>
          {props.sprints && props.sprints['hydra:member'].map((sprint, index) => (
            <th key={sprint.id} colSpan={2} className={classes.headerCell}
                title={`${new Date(sprint.startDate).toLocaleDateString()}`}>{`Sprint ${index + 1}`}</th>
          ))}
        </tr>
        </thead>
        <tbody>
        {props.teams && props.teams['hydra:member'].map(team => (
          <tr key={team.id}>
            <td>{team.name}</td>
            <td title="Total remaining frontend capacity">{remainingCapacity[team.id].total.frontend}</td>
            <td title="Total remaining backend capacity">{remainingCapacity[team.id].total.backend}</td>
            {props.sprints && props.sprints['hydra:member'].map(sprint => (
              <Fragment key={`${team.id}:${sprint.id}`}>
                <td title="Remaining frontend capacity">{remainingCapacity[team.id][sprint.id].frontend}</td>
                <td title="Remaining backend capacity">{remainingCapacity[team.id][sprint.id].backend}</td>
              </Fragment>
            ))}
          </tr>
        ))}
        </tbody>
      </table>
    </div>

  );
}

TeamSprintCapacity.propTypes = {
  teams: PropTypes.object,
  sprints: PropTypes.object,
  capacity: PropTypes.arrayOf(PropTypes.object),
  estimate: PropTypes.arrayOf(PropTypes.object),
};

const mapStateToProps = state => ({
  teams: state.team.list.retrieved,
  sprints: state.sprint.list.retrieved,
  programIncrement: state.programincrement.show.retrieved,
  estimate: state.estimate.list.retrieved,
});

export default connect(mapStateToProps)(TeamSprintCapacity);
