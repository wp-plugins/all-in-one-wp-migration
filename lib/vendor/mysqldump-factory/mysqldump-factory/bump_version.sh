#!/bin/bash

if [ -z "$1" ]; then
  echo -e "\033[0;31mUsage:\033[0m $0 (major|minor|patch)\033[0m"
  echo -e "\033[4;32mCurrent version\033[0m:\033[0m \033[33m`git describe master`\033[0m"
  exit -1
fi

# increment version depending on passed parameter
case "$1" in
  major)
    currentVersion=`git describe master`
    bumpedVersion=`echo $currentVersion | ( IFS=".$IFS" ; read a b c && echo $((a + 1)).0.0 )`
    ;;
  minor)
    currentVersion=`git describe master`
    bumpedVersion=`echo $currentVersion | ( IFS=".$IFS" ; read a b c && echo $a.$((b + 1)).0 )`
    ;;
  patch)
    currentVersion=`git describe master`
    bumpedVersion=`echo $currentVersion | ( IFS=".$IFS" ; read a b c && echo $a.$b.$((c + 1)) )`
    ;;
  *)
    echo -e "\033[0;31mUsage:\033[0m $0 (major|minor|patch)\033[0m"
    echo -e "\033[4;32mCurrent version\033[0m:\033[0m \033[33m`git describe master`\033[0m"
    exit -1
esac

# let's start a new release
git flow release start $bumpedVersion

# bump version in all files
for file in `find . -path ./coverage -prune -o -path ./.git -prune -o -type f`
do
  filename=$(basename "$file")
  ext="${filename##*.}"
  if [ $ext != "png" -a $ext != "jpg" -a $ext != "jpeg" -a $ext != "gif" ]; then
    if [ $ext != "DS_Store" -a $ext != "ttf" -a $ext != "node_modules" -a $ext != "git" ]; then
      sed -i '' "s/GIT: $currentVersion/GIT: $bumpedVersion/g" $file
    fi
  fi
done

# bump version in package.json
sed -i '' "s/\"version\": \"$currentVersion\"/\"version\": \"$bumpedVersion\"/g" package.json

# add changed files to git
git add . && git commit -m "Bumped version from $currentVersion to $bumpedVersion"

# finish the release
git flow release finish -F -m "$bumpedVersion" $bumpedVersion

# publish develop branch
git checkout develop && git push origin develop

# publish master
git checkout master && git push origin master

# publish tags
git push origin --tags

# Announce the result
echo -e "\033[4;32mSuccessfully released\033[0m:\033[0m \033[33m$bumpedVersion\033[0m"
