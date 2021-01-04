import React, {Fragment} from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {makeStyles} from '@material-ui/core/styles';
import sortBy from 'lodash/sortBy';
import ExpansionPanel from '@material-ui/core/ExpansionPanel';
import ExpansionPanelSummary from '@material-ui/core/ExpansionPanelSummary';
import ExpansionPanelDetails from '@material-ui/core/ExpansionPanelDetails';
import Typography from '@material-ui/core/Typography';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore'

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

function getTeamSprintCapacity(teams, projectsSettings, estimate) {
  const result = {};
  const defaultCapacity = {frontend: 0, backend: 0};

  for (let settings of projectsSettings) {
    let capacity = settings.teamSprintCapacities;
    for (let team of filterTeamsByCapacity(teams, capacity)) {
      let teamId = team['@id'];
      let teamCapacity = {total: {...defaultCapacity}, totalInitial: {...defaultCapacity}};
      for (let sprint of settings.sprints) {
        let initialCapacity = getInitialCapacity(teamId, sprint, capacity);
        let filledCapacity = getFilledCapacity(teamId, sprint, estimate);
        let remainingCapacity = {
          frontend: initialCapacity.frontend - filledCapacity.frontend,
          backend: initialCapacity.backend - filledCapacity.backend,
        };
        teamCapacity[sprint] = remainingCapacity;
        teamCapacity.total.frontend += remainingCapacity.frontend;
        teamCapacity.total.backend += remainingCapacity.backend;
        teamCapacity.totalInitial.frontend += initialCapacity.frontend;
        teamCapacity.totalInitial.backend += initialCapacity.backend;
      }
      result[teamId] = teamCapacity;
    }
  }
  return result;
}

const useStyles = makeStyles(theme => ({
  root: {
    position: 'sticky',
    top: 0,
    zIndex: theme.zIndex.speedDial,
    background: theme.palette.background.default,
  },
  capacityContainer: {
    flexDirection: 'column',
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

function filterTeamsByCapacity(teams, teamSprintCapacities) {
  let result = new Set()
  teamSprintCapacities.forEach(tc => result.add(teams.find(t => t['@id'] === tc.team)));
  return [...result];
}

const TeamSprintCapacity = props => {
  const classes = useStyles(props);
  if (!props.teams || !props.sprints || !props.programIncrement || !props.estimate) {
    return null;
  }

  const sprints = props.sprints['hydra:member'];
  let sprintsBySchedule = {};
  for (let sprint of sprints) {
    let schedule = sprint.schedule;
    if (sprintsBySchedule[schedule]) {
      sprintsBySchedule[schedule].push(sprint);
    } else {
      sprintsBySchedule[schedule] = [sprint];
    }
  }
  sprintsBySchedule = Object.entries(sprintsBySchedule);

  const teams = props.teams['hydra:member'];
  const teamsBySchedule = {};
  for (let settings of props.programIncrement.projectsSettings) {
    let schedule = sprintsBySchedule.find(([schedule, scheduleSprints]) => settings.sprints.includes(scheduleSprints[0]['@id']))[0];
    let relatedTeams = filterTeamsByCapacity(teams, settings.teamSprintCapacities);
    if (teamsBySchedule[schedule]) {
      teamsBySchedule[schedule].push(...relatedTeams);
    } else {
      teamsBySchedule[schedule] = relatedTeams;
    }
  }

  const capacity = getTeamSprintCapacity(teams, props.programIncrement.projectsSettings, props.estimate);
  // Sort by count of sprints to improve usability (rows with fewer sprints are shown upper in the table)
  sprintsBySchedule = sortBy(sprintsBySchedule, o => o[1].length);
  return (
    <div className={classes.root}>
      <ExpansionPanel>
        <ExpansionPanelSummary expandIcon={<ExpandMoreIcon/>}>
          <Typography>Remaining capacity</Typography>
        </ExpansionPanelSummary>
        <ExpansionPanelDetails classes={{root: classes.capacityContainer}}>
          {sprintsBySchedule.map(([schedule, scheduleSprints]) => {
            return (
              <table key={schedule} className={classes.table}>
                <thead>
                <tr>
                  <th className={classes.headerCell}>Team</th>
                  <th className={classes.headerCell} colSpan={2} title="Remaining capacity">Total</th>
                  {scheduleSprints.map((sprint, index) => (
                    <th key={sprint.id} colSpan={2} className={classes.headerCell}
                        title={`${new Date(sprint.startDate).toLocaleDateString()} - ${new Date(sprint.endDate).toLocaleDateString()}`}>{`Sprint ${index + 1}`}</th>
                  ))}
                </tr>
                </thead>
                <tbody>
                {sortBy(teamsBySchedule[schedule], 'name').map(team => {
                  let teamCapacity = capacity[team['@id']];
                  return (
                    <tr key={team['@id']}>
                      <td>{team.name}</td>
                      <td title="Frontend">{teamCapacity.total.frontend}</td>
                      <td title="Backend">{teamCapacity.total.backend}</td>
                      {scheduleSprints.map(sprint => (
                        <Fragment key={`${team['@id']}:${sprint['@id']}`}>
                          <td title="Frontend">{teamCapacity[sprint['@id']].frontend}</td>
                          <td title="Backend">{teamCapacity[sprint['@id']].backend}</td>
                        </Fragment>
                      ))}
                    </tr>
                  )
                })}
                </tbody>
              </table>
            );
          })}
        </ExpansionPanelDetails>
      </ExpansionPanel>
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
