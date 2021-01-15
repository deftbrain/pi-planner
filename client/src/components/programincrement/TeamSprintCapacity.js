import React, {Fragment} from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {makeStyles} from '@material-ui/core/styles';
import sortBy from 'lodash/sortBy';

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

function filterTeamsByCapacity(teams, teamSprintCapacities) {
  let result = new Set()
  teamSprintCapacities.forEach(tc => {
    const team = teams.find(t => t['@id'] === tc.team);
    if (team) {
      result.add(team)
    }
  });
  return [...result];
}

function formatCapacity(capacity) {
  return Math.round((capacity + Number.EPSILON) * 100) / 100;
}

const useStyles = makeStyles(theme => ({
  table: {
    tableLayout: 'fixed',
    width: '100%',
    borderCollapse: 'collapse',
    '& th,td': {
      border: '1px solid black',
      textAlign: 'center',
    },
    '& th:first-child': {
      width: '8%',
    },
    '& th:nth-child(2)': {
      width: '7%',
    },
  },
  overcapacity: {
    color: theme.palette.error.dark,
  },
  headerCell: theme.typography.subtitle2,
}));

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

  const teams = props.teams['hydra:member'].filter(t => !props.selectedTeam || t['@id'] === props.selectedTeam);
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
  sprintsBySchedule = sprintsBySchedule.filter(([schedule]) => teamsBySchedule[schedule].length);
  // Sort by count of sprints to improve usability (rows with fewer sprints are shown upper in the table)
  sprintsBySchedule = sortBy(sprintsBySchedule, o => o[1].length);
  return (
    <div>
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
                  <td title="Frontend" className={teamCapacity.total.frontend < 0 ? classes.overcapacity : undefined}>
                    {formatCapacity(teamCapacity.total.frontend)}
                  </td>
                  <td title="Backend" className={teamCapacity.total.backend < 0 ? classes.overcapacity : undefined}>
                    {formatCapacity(teamCapacity.total.backend)}
                  </td>
                  {scheduleSprints.map(sprint => (
                    <Fragment key={`${team['@id']}:${sprint['@id']}`}>
                      <td title="Frontend"
                          className={teamCapacity[sprint['@id']].frontend < 0 ? classes.overcapacity : undefined}>
                        {formatCapacity(teamCapacity[sprint['@id']].frontend)}
                      </td>
                      <td title="Backend"
                          className={teamCapacity[sprint['@id']].backend < 0 ? classes.overcapacity : undefined}>
                        {formatCapacity(teamCapacity[sprint['@id']].backend)}
                      </td>
                    </Fragment>
                  ))}
                </tr>
                  )
                })}
                </tbody>
              </table>
            );
          })}
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
  selectedTeam: state.programincrement.show.teamFilter,
  estimate: state.estimate.list.retrieved
});

export default connect(mapStateToProps)(TeamSprintCapacity);
