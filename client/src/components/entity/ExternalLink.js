import React from 'react';
import Link from '@material-ui/core/Link';
import LaunchIcon from '@material-ui/icons/Launch';
import {makeStyles} from '@material-ui/core/styles';

function stopPropagation(e) {
  e.stopPropagation();
}

const useStyles = makeStyles(theme => ({
  icon: {
    fontSize: 'small',
    padding: `0 ${theme.spacing(0.5)}px`,
  }
}))

export default function (props) {
  const classes = useStyles();

  return <Link href={props.entity['externalUrl']} target="_blank" onClick={stopPropagation} onFocus={stopPropagation}
               aria-label="External link">{props.entity['name']}<LaunchIcon className={classes.icon}/>
  </Link>
}
