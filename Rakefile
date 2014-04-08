require 'date'
require 'digest/md5'
require 'fileutils'
require 'json'

basedir = "."
build   = "#{basedir}/build"
source  = "#{basedir}/src/ImboClientCli"
logs    = "#{build}/logs"

desc "Task used by Jenkins-CI"
task :jenkins => [:prepare, :lint, :cs_ci, :test]

desc "Default task"
task :default => [:prepare, :lint, :cs, :test, :readthedocs]

desc "Clean up and create artifact directories"
task :prepare do
  FileUtils.rm_rf build
  FileUtils.mkdir build

  ["coverage", "logs", "docs"].each do |d|
    FileUtils.mkdir "#{build}/#{d}"
  end
end

desc "Spell check and generate end user docs"
task :readthedocs do
  wd = Dir.getwd
  Dir.chdir("docs")
  begin
    sh %{make spelling}
  rescue Exception
    puts "Spelling error in the docs, aborting"
    exit 1
  end
  puts "No spelling errors. Generate docs"
  sh %{make html}
  Dir.chdir(wd)
end

desc "Check syntax on all php files in the project"
task :lint do
  lintCache = "#{basedir}/.lintcache"

  begin
    sums = JSON.parse(IO.read(lintCache))
  rescue Exception => foo
    sums = {}
  end

  `git ls-files "*.php"`.split("\n").each do |f|
    f = File.absolute_path(f)
    md5 = Digest::MD5.hexdigest(File.read(f))

    next if sums[f] == md5

    sums[f] = md5

    begin
      sh %{php -l #{f}}
    rescue Exception
      exit 1
    end
  end

  IO.write(lintCache, JSON.dump(sums))
end

desc "Run tests"
task :test do
  begin
    sh %{vendor/bin/phpunit --verbose -c tests --coverage-html build/coverage --coverage-clover build/logs/clover.xml --log-junit build/logs/junit.xml}
  rescue Exception
    exit 1
  end
end

desc "Check coding standard"
task :cs do
  system "phpcs --standard=Imbo #{source}"
end

desc "Check coding standard and generate checkstyle logs"
task :cs_ci do
  system "phpcs --report=checkstyle --report-file=#{logs}/checkstyle.xml --standard=Imbo #{source}"
end
