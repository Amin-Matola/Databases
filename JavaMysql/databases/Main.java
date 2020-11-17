package JavaMysql.databases;

import java.util.HashMap;
import java.util.Map;

public class Main {

    public static void main(String[] args) {
	
        // Create Columns and their descriptions
        Map<String, Object> columns = new HashMap<>();
        columns.put("id", "INT PRIMARY KEY AUTO_INCREMENT");
        columns.put("name", "text");
        columns.put("age", "int not null")
        
        // Create new table with the specified columns
        Table table     = new Table("example-table", columns)
    }
}
