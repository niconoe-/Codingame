#include <Foundation/Foundation.h>

int main(int argc, const char * argv[]) {

    // game loop
    while (1) {
        char enemy1[256];
        scanf("%s", enemy1);
        int dist1;
        scanf("%d", &dist1);
        char enemy2[256];
        scanf("%s", enemy2);
        int dist2;
        scanf("%d", &dist2);
        
        if (dist1 < dist2) {
            printf("%s\n", enemy1);
        } else {
            printf("%s\n", enemy2);
        }
    }
}