#!/bin/sh
if [ ! $1 ]
then
    echo 'param sha1 missed'
    exit
fi
old_hash=$1

if [ ! $2 ]
then
    echo "pulling from git repo:"
    git pull
    new_hash=`git log -n 1 --pretty=format:"%H"`
else
    new_hash=$2
fi
echo "building from:"
echo `git log ${old_hash} -n 1 --pretty=format:"%h | %ad | %an | %s"`
echo "to:"
echo `git log ${new_hash} -n 1 --pretty=format:"%h | %ad | %an | %s"`

#echo "differences:"
#echo ""
# echo "git diff --name-status ${old_hash} ${new_hash} | awk '/^[^DR]/{print \"\\\"\"substr(\$0, 3)\"\\\"\"}'"
# exit; #debug

git diff --name-status ${old_hash} ${new_hash} | awk '/^[^DR]/{print "\""substr($0, 3)"\""}'
# exit; #debug

# git生成代码压缩包 # 排除掉删除文件列表
git diff --name-status $old_hash $new_hash | awk '/^[^DR]/{print "\""substr($0, 3)"\""}' >> C_R_FILES.txt
git diff --name-status $old_hash $new_hash | awk '/^[R]/{print "\""$3"\""}' >> C_R_FILES.txt
cat C_R_FILES.txt | xargs git archive -o ./__git_archive.tar $new_hash 
echo "generated: ./__git_archive.tar"


mkdir __git_archive
tar xf __git_archive.tar -C __git_archive
cd __git_archive

git diff $old_hash $new_hash --name-status | awk -F"\t" '/^[DR]/{print $0}' >> 文件删除.txt
git diff $old_hash $new_hash --name-status | awk -F"\t" '/^[R]/{print $2}' >> 文件删除.txt

cd ..
rm -f __git_archive.zip

echo generated dir: __git_archive
