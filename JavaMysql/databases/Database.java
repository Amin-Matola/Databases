package JavaMysql.databases;


import java.sql.*;

public abstract class Database {

    private static String host, db, username, passwd, url, admin;
    private static Config config;
    private Connection __conn         = null;
    private Statement state           = null;
    ResultSet results                 = null;

    public void init () {
        config      = new Config();
        host        = config.HOST;
        db          = config.DB;
        username    = config.USERNAME;
        passwd      = config.PASSWD;

        if( ! host.isEmpty() ) {
            host    = f("jdbc:mysql://%s%s",( host.endsWith("/") ? host.substring(0, host.length() - 1):host),":3306/");
        }

        connect();

    }

    public static String f(String master, Object ...formats){
        return String.format(master, formats);
    }

    public static String join(String del, String[] formats){
        return String.join(del, formats);
    }

    boolean connect() {
        try {
            Class.forName("com.mysql.jdbc.Driver");
            __conn = DriverManager.getConnection(host + db, username, passwd);

            if( __conn == null )
                return false;
            state = __conn.createStatement();
        } catch (Exception e){
            print(e);
        }
        return true;
    }

    public void createDatabase( String db, Statement state){
        if( db.isEmpty() || state == null )
            return;
        if( state != null )
            this.state      = state;
        if( !db.isEmpty() )
            this.db         = db;
        try {
            this.state.execute(f("CREATE DATABASE IF NOT EXISTS %s", db));
        }catch (Exception e){

        }

    }

    public ResultSet run(String query, Boolean ...results) {
        try {
            if ( results.length >  0 )
                this.results = this.state.executeQuery(query);
            else
                this.state.execute(query);
        } catch( Exception e){
            print(e);
        }
        return this.results;
    }

    /* *
     * Get the connection object
     * */
    public Connection getConnection(){
        if(this.__conn == null ){
            this.connect();
        }
        return this.__conn;
    }

    /* *
     * Try re-establish connection if it wasn't successful
     * */
    private void reconnect(String db){
        if(!db.isEmpty())
             this.db    = db;

        this.connect();
    }

    String getHost(){
        return host;
    }

    String getDb() {
        return db;
    }

    String getUser() {
        return username;
    }

    String getUrl() {
        return url;
    }

    Config getConfig(){
        return Database.config;
    }

    public static void print(Object data) {
        System.out.println(data);
    }
}
