#!/usr/bin/env sh

echo 'Removing previously set integration configs...'
rm -fv api/config/*jira.yaml
rm -fv api/config/**/*jira.yaml
rm -fv api/config/*version_one.yaml
rm -fv api/config/**/*version_one.yaml

JIRA=Jira
V1=VersionOne

if [ "$1" != $JIRA -a "$1" != $V1 ]; then
	echo "The target system is not set or invalid.\nUsage: api/bin/enable-integration.sh $JIRA|$V1"
	exit 1
fi

message="\nCopying integration configs..."
if [ "$1" == $JIRA ]; then
	echo $message
	docker-compose run --rm --no-deps api envsubst < api/src/Integration/Jira/Resources/config/serializer.template.yaml > api/config/serializer_jira.yaml
	echo 'api/src/Integration/Jira/Resources/config/serializer.template.yaml > api/config/serializer_jira.yaml'
	cp -v api/src/Integration/Jira/Resources/config/httplug.yaml api/config/packages/httplug_jira.yaml
	cp -v api/src/Integration/Jira/Resources/config/routing.yaml api/config/routes/jira.yaml
	cp -v api/src/Integration/Jira/Resources/config/services.yaml api/config/services/jira.yaml
elif [ "$1" == $V1 ]; then
	echo $message
	cp -v api/src/Integration/VersionOne/Resources/config/httplug.yaml api/config/packages/httplug_version_one.yaml
	cp -v api/src/Integration/VersionOne/Resources/config/messenger.yaml api/config/packages/messenger_version_one.yaml
	cp -v api/src/Integration/VersionOne/Resources/config/services.yaml api/config/services/version_one.yaml
fi
