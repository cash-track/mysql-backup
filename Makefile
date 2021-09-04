include .env
export

# Local config
CONTAINER_NAME=cashtrack_mysql_backup

# Deploy config
REPO=cashtrack/mysql-backup
IMAGE_RELEASE=$(REPO):$(RELEASE_VERSION)
IMAGE_DEV=$(REPO):dev
IMAGE_LATEST=$(REPO):latest

.PHONY: build tag push start stop

build:
	docker build . -t $(IMAGE_DEV) --no-cache

tag:
	docker tag $(IMAGE_DEV) $(IMAGE_RELEASE)
	docker tag $(IMAGE_DEV) $(IMAGE_LATEST)

push:
	docker push $(IMAGE_RELEASE)
	docker push $(IMAGE_LATEST)

start:
	docker run \
	  --rm \
      --name $(CONTAINER_NAME) \
      --net cash-track-local \
      --env-file .env \
      $(IMAGE_DEV)

stop:
	docker stop $(CONTAINER_NAME)
